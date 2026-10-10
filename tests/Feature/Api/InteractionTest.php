<?php

namespace Tests\Feature\Api;

use App\Models\Booking;
use App\Models\Comment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\MakesMarketplaceData;
use Tests\TestCase;

class InteractionTest extends TestCase
{
    use RefreshDatabase, MakesMarketplaceData;

    private User $dealer;
    private Property $property;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedLookups();
        $this->dealer = $this->makeDealer();
        $this->property = $this->makeProperty($this->dealer);
    }

    // ---------- comments ----------

    public function test_anyone_can_read_comments_and_anonymous_authors_stay_hidden(): void
    {
        $author = $this->makeUser(attrs: ['name' => 'Secret Author']);
        Comment::create(['user_id' => $author->id, 'Property_id' => $this->property->id, 'comment_type_id' => 1, 'Description' => 'Public one']);
        Comment::create(['user_id' => $author->id, 'Property_id' => $this->property->id, 'comment_type_id' => 2, 'Description' => 'Hidden one']);

        $response = $this->getJson("/api/v1/properties/{$this->property->id}/comments")->assertOk();

        $byText = collect($response->json('data'))->keyBy('description');
        $this->assertSame('Secret Author', $byText['Public one']['author']['name']);
        $this->assertTrue($byText['Hidden one']['anonymous']);
        $this->assertNull($byText['Hidden one']['author']);
        $this->assertStringNotContainsString('Secret Author', json_encode($byText['Hidden one']));
    }

    public function test_user_can_comment_and_delete_only_their_own(): void
    {
        $me = $this->makeUser();
        $other = $this->makeUser();
        $theirs = Comment::create(['user_id' => $other->id, 'Property_id' => $this->property->id, 'comment_type_id' => 1, 'Description' => 'not mine']);
        Sanctum::actingAs($me);

        $created = $this->postJson("/api/v1/properties/{$this->property->id}/comments", ['description' => 'Nice place', 'anonymous' => true])
            ->assertCreated()
            ->assertJsonPath('data.anonymous', true)
            ->assertJsonPath('data.is_mine', true);

        $this->deleteJson('/api/v1/comments/' . $theirs->id)->assertForbidden();
        $this->deleteJson('/api/v1/comments/' . $created->json('data.id'))->assertNoContent();
    }

    public function test_comment_requires_a_body_and_authentication(): void
    {
        $this->postJson("/api/v1/properties/{$this->property->id}/comments", ['description' => 'x'])->assertUnauthorized();

        Sanctum::actingAs($this->makeUser());
        $this->postJson("/api/v1/properties/{$this->property->id}/comments", ['description' => ''])
            ->assertUnprocessable();
    }

    public function test_admin_can_delete_any_comment(): void
    {
        $comment = Comment::create(['user_id' => $this->makeUser()->id, 'Property_id' => $this->property->id, 'comment_type_id' => 1, 'Description' => 'spam']);
        Sanctum::actingAs($this->makeAdmin());

        $this->deleteJson("/api/v1/comments/{$comment->id}")->assertNoContent();
    }

    // ---------- ratings ----------

    public function test_rating_is_one_per_user_and_can_be_changed(): void
    {
        $a = $this->makeUser();
        $b = $this->makeUser();

        Sanctum::actingAs($a);
        $this->putJson("/api/v1/properties/{$this->property->id}/rating", ['rating' => 2])->assertOk();
        $this->putJson("/api/v1/properties/{$this->property->id}/rating", ['rating' => 4])->assertOk();

        Sanctum::actingAs($b);
        $this->putJson("/api/v1/properties/{$this->property->id}/rating", ['rating' => 5])
            ->assertOk()
            ->assertJsonPath('rating_count', 2)
            ->assertJsonPath('rating_avg', 4.5);
    }

    public function test_rating_must_be_between_one_and_five(): void
    {
        Sanctum::actingAs($this->makeUser());

        foreach ([0, 6, 'abc'] as $bad) {
            $this->putJson("/api/v1/properties/{$this->property->id}/rating", ['rating' => $bad])->assertUnprocessable();
        }
    }

    // ---------- favorites ----------

    public function test_favorites_add_is_idempotent_and_remove_works(): void
    {
        $user = $this->makeUser();
        Sanctum::actingAs($user);

        $this->putJson("/api/v1/properties/{$this->property->id}/favorite")->assertOk();
        $this->putJson("/api/v1/properties/{$this->property->id}/favorite")->assertOk();
        $this->getJson('/api/v1/favorites')->assertOk()->assertJsonCount(1, 'data');

        $this->deleteJson("/api/v1/properties/{$this->property->id}/favorite")->assertNoContent();
        $this->getJson('/api/v1/favorites')->assertJsonCount(0, 'data');
    }

    public function test_favorites_are_private_to_each_user(): void
    {
        $this->makeUser()->Favourite_Property()->attach($this->property->id);
        Sanctum::actingAs($this->makeUser());

        $this->getJson('/api/v1/favorites')->assertJsonCount(0, 'data');
    }

    // ---------- bookings ----------

    private function bookingDate(): string
    {
        return now()->addDays(2)->toDateTimeString();
    }

    public function test_user_can_book_and_rebooking_updates_instead_of_duplicating(): void
    {
        $user = $this->makeUser();
        Sanctum::actingAs($user);

        $this->postJson("/api/v1/properties/{$this->property->id}/bookings", ['date' => $this->bookingDate(), 'description' => 'first'])
            ->assertCreated()
            ->assertJsonPath('data.status', 'Pending')
            ->assertJsonPath('data.dealer_id', $this->dealer->id);

        $this->postJson("/api/v1/properties/{$this->property->id}/bookings", ['date' => now()->addDays(3)->toDateTimeString(), 'description' => 'second'])
            ->assertOk()
            ->assertJsonPath('data.description', 'second');

        $this->assertSame(1, Booking::where('user_id', $user->id)->count());
    }

    public function test_booking_date_must_be_in_the_future(): void
    {
        Sanctum::actingAs($this->makeUser());

        $this->postJson("/api/v1/properties/{$this->property->id}/bookings", ['date' => now()->subDay()->toDateTimeString()])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('date');
    }

    public function test_each_role_sees_the_right_bookings(): void
    {
        $alice = $this->makeUser();
        $bob = $this->makeUser();
        $otherDealer = $this->makeDealer();
        $otherProperty = $this->makeProperty($otherDealer);

        foreach ([[$alice, $this->property], [$bob, $this->property], [$alice, $otherProperty]] as [$u, $p]) {
            Sanctum::actingAs($u);
            $this->postJson("/api/v1/properties/{$p->id}/bookings", ['date' => $this->bookingDate()])->assertSuccessful();
        }

        Sanctum::actingAs($alice);
        $this->getJson('/api/v1/bookings')->assertJsonCount(2, 'data');

        Sanctum::actingAs($this->dealer);
        $this->getJson('/api/v1/bookings')->assertJsonCount(2, 'data');

        Sanctum::actingAs($this->makeAdmin());
        $this->getJson('/api/v1/bookings')->assertJsonCount(3, 'data');
    }

    public function test_only_the_properties_dealer_can_decide_a_booking(): void
    {
        $booking = Booking::create([
            'user_id' => $this->makeUser()->id, 'dealer_id' => $this->dealer->id, 'property_id' => $this->property->id,
            'date' => $this->bookingDate(), 'description' => 'x', 'status' => 'Pending',
        ]);

        Sanctum::actingAs($this->makeDealer());
        $this->patchJson("/api/v1/bookings/{$booking->id}", ['status' => 'Approved'])->assertForbidden();

        Sanctum::actingAs($this->makeUser());
        $this->patchJson("/api/v1/bookings/{$booking->id}", ['status' => 'Approved'])->assertForbidden();

        Sanctum::actingAs($this->dealer);
        $this->patchJson("/api/v1/bookings/{$booking->id}", ['status' => 'Bogus'])->assertUnprocessable();
        $this->patchJson("/api/v1/bookings/{$booking->id}", ['status' => 'Approved'])
            ->assertOk()
            ->assertJsonPath('data.status', 'Approved');
    }

    public function test_booking_response_includes_property_summary(): void
    {
        Sanctum::actingAs($user = $this->makeUser());
        $this->postJson("/api/v1/properties/{$this->property->id}/bookings", ['date' => $this->bookingDate()]);

        $this->getJson('/api/v1/bookings')
            ->assertJsonPath('data.0.property.id', $this->property->id)
            ->assertJsonPath('data.0.property.address', '1 Test Street');
    }
}
