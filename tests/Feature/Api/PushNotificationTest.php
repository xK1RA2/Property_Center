<?php

namespace Tests\Feature\Api;

use App\Models\DeviceToken;
use App\Services\PushNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\MakesMarketplaceData;
use Tests\TestCase;

class PushNotificationTest extends TestCase
{
    use RefreshDatabase, MakesMarketplaceData;

    private const TOKEN = 'ExponentPushToken[abcdefghijklmnopqrstuv]';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedLookups();
    }

    // ---------- registering devices ----------

    public function test_device_registration_requires_authentication_and_a_real_expo_token(): void
    {
        $this->postJson('/api/v1/devices', ['token' => self::TOKEN])->assertUnauthorized();

        Sanctum::actingAs($this->makeUser());
        $this->postJson('/api/v1/devices', ['token' => 'not-a-token'])->assertUnprocessable();
        $this->postJson('/api/v1/devices', [])->assertUnprocessable();
    }

    public function test_registering_is_idempotent_and_moves_the_token_between_users(): void
    {
        $first = $this->makeUser();
        $second = $this->makeUser();

        Sanctum::actingAs($first);
        $this->postJson('/api/v1/devices', ['token' => self::TOKEN, 'platform' => 'android'])->assertCreated();
        $this->postJson('/api/v1/devices', ['token' => self::TOKEN, 'platform' => 'android'])->assertOk();
        $this->assertSame(1, DeviceToken::count());

        // someone else signs in on the same phone
        Sanctum::actingAs($second);
        $this->postJson('/api/v1/devices', ['token' => self::TOKEN])->assertOk();
        $this->assertSame($second->id, DeviceToken::first()->user_id);
    }

    public function test_unregistering_only_removes_your_own_token(): void
    {
        $owner = $this->makeUser();
        DeviceToken::create(['user_id' => $owner->id, 'token' => self::TOKEN]);

        Sanctum::actingAs($this->makeUser());
        $this->deleteJson('/api/v1/devices', ['token' => self::TOKEN])->assertNoContent();
        $this->assertSame(1, DeviceToken::count());

        Sanctum::actingAs($owner);
        $this->deleteJson('/api/v1/devices', ['token' => self::TOKEN])->assertNoContent();
        $this->assertSame(0, DeviceToken::count());
    }

    // ---------- sending ----------

    public function test_dealer_is_notified_when_someone_books_a_visit(): void
    {
        Http::fake(['exp.host/*' => Http::response(['data' => [['status' => 'ok']]])]);
        $dealer = $this->makeDealer();
        DeviceToken::create(['user_id' => $dealer->id, 'token' => self::TOKEN]);
        $property = $this->makeProperty($dealer, ['address' => '9 Push Street']);

        Sanctum::actingAs($this->makeUser(attrs: ['name' => 'Layla']));
        $this->postJson("/api/v1/properties/{$property->id}/bookings", ['date' => now()->addDay()->toDateTimeString()])
            ->assertCreated();

        Http::assertSent(function (Request $request) {
            $message = $request->data()[0] ?? [];

            return $request->url() === 'https://exp.host/--/api/v2/push/send'
                && $message['to'] === self::TOKEN
                && $message['title'] === 'New visit request'
                && str_contains($message['body'], 'Layla')
                && str_contains($message['body'], '9 Push Street')
                && $message['data']['screen'] === 'bookings';
        });
    }

    public function test_requester_is_notified_of_the_dealers_decision(): void
    {
        Http::fake(['exp.host/*' => Http::response(['data' => [['status' => 'ok']]])]);
        $dealer = $this->makeDealer();
        $user = $this->makeUser();
        DeviceToken::create(['user_id' => $user->id, 'token' => self::TOKEN]);
        $property = $this->makeProperty($dealer);

        Sanctum::actingAs($user);
        $id = $this->postJson("/api/v1/properties/{$property->id}/bookings", ['date' => now()->addDay()->toDateTimeString()])
            ->json('data.id');

        Sanctum::actingAs($dealer);
        $this->patchJson("/api/v1/bookings/{$id}", ['status' => 'Rejected'])->assertOk();

        Http::assertSent(fn (Request $r) => ($r->data()[0]['title'] ?? null) === 'Visit declined'
            && $r->data()[0]['to'] === self::TOKEN);
    }

    public function test_nothing_is_sent_when_the_user_has_no_registered_device(): void
    {
        Http::fake();
        $dealer = $this->makeDealer();
        $property = $this->makeProperty($dealer);

        Sanctum::actingAs($this->makeUser());
        $this->postJson("/api/v1/properties/{$property->id}/bookings", ['date' => now()->addDay()->toDateTimeString()])
            ->assertCreated();

        Http::assertNothingSent();
    }

    public function test_dead_tokens_are_pruned_when_expo_reports_them(): void
    {
        $user = $this->makeUser();
        $live = 'ExponentPushToken[liveliveliveliveliveli]';
        DeviceToken::create(['user_id' => $user->id, 'token' => self::TOKEN]);
        DeviceToken::create(['user_id' => $user->id, 'token' => $live]);

        Http::fake(['exp.host/*' => Http::response(['data' => [
            ['status' => 'error', 'details' => ['error' => 'DeviceNotRegistered']],
            ['status' => 'ok'],
        ]])]);

        // order of tokens in the request matches the order of tickets in the response
        $tokens = DeviceToken::where('user_id', $user->id)->orderBy('id')->pluck('token')->all();
        $this->assertSame([self::TOKEN, $live], $tokens);

        app(PushNotifier::class)->toUser($user, 'Hi', 'There');

        $this->assertSame([$live], DeviceToken::pluck('token')->all());
    }

    public function test_a_push_outage_never_breaks_the_booking(): void
    {
        Http::fake(['exp.host/*' => fn () => throw new \RuntimeException('connection refused')]);
        $dealer = $this->makeDealer();
        DeviceToken::create(['user_id' => $dealer->id, 'token' => self::TOKEN]);
        $property = $this->makeProperty($dealer);

        Sanctum::actingAs($this->makeUser());
        $this->postJson("/api/v1/properties/{$property->id}/bookings", ['date' => now()->addDay()->toDateTimeString()])
            ->assertCreated();
    }

    public function test_an_http_error_from_expo_is_tolerated(): void
    {
        Http::fake(['exp.host/*' => Http::response('boom', 500)]);
        $user = $this->makeUser();
        DeviceToken::create(['user_id' => $user->id, 'token' => self::TOKEN]);

        app(PushNotifier::class)->toUser($user, 'Hi', 'There');

        $this->assertSame(1, DeviceToken::count()); // not pruned on a server error
    }
}
