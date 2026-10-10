<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\Sanctum;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialUser;
use Mockery;
use Tests\Concerns\MakesMarketplaceData;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase, MakesMarketplaceData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedLookups();
    }

    // ---------- mass assignment ----------

    public function test_role_and_social_ids_cannot_be_mass_assigned(): void
    {
        $user = new User([
            'name' => 'Mallory', 'email' => 'm@example.com',
            'role_id' => User::ROLE_ADMIN, 'google_id' => '1', 'facebook_id' => '2',
        ]);

        $this->assertNull($user->role_id);
        $this->assertNull($user->google_id);
        $this->assertNull($user->facebook_id);
        $this->assertSame('Mallory', $user->name);
    }

    public function test_registration_ignores_role_and_social_ids_sent_by_the_client(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Eve', 'email' => 'eve@example.com', 'password' => 'abcdefg1', 'password_confirmation' => 'abcdefg1',
            'role_id' => 3, 'google_id' => 'x',
        ])->assertCreated()->assertJsonPath('user.role_id', User::ROLE_USER);

        $this->assertNull(User::where('email', 'eve@example.com')->value('google_id'));
    }

    // ---------- privacy of other people's data ----------

    public function test_a_commenters_phone_and_username_are_not_public(): void
    {
        $dealer = $this->makeDealer();
        $property = $this->makeProperty($dealer);
        $commenter = $this->makeUser(attrs: ['name' => 'Layla', 'phone' => '0795550000', 'username' => 'layla_private']);
        Comment::create(['user_id' => $commenter->id, 'Property_id' => $property->id, 'comment_type_id' => 1, 'Description' => 'hi']);

        $author = $this->getJson("/api/v1/properties/{$property->id}/comments")->json('data.0.author');

        $this->assertSame('Layla', $author['name']);
        $this->assertArrayNotHasKey('phone', $author);
        $this->assertArrayNotHasKey('username', $author);
        $this->assertArrayNotHasKey('email', $author);
    }

    public function test_a_dealers_contact_details_stay_public(): void
    {
        $dealer = $this->makeDealer(['phone' => '0791112222']);
        $property = $this->makeProperty($dealer);

        $this->getJson("/api/v1/properties/{$property->id}")
            ->assertJsonPath('data.dealer.phone', '0791112222')
            ->assertJsonMissingPath('data.dealer.email');
    }

    public function test_the_owner_and_admins_see_private_fields_other_users_do_not(): void
    {
        $user = $this->makeUser(attrs: ['phone' => '0793334444']);
        $property = $this->makeProperty($this->makeDealer());
        Comment::create(['user_id' => $user->id, 'Property_id' => $property->id, 'comment_type_id' => 1, 'Description' => 'x']);

        Sanctum::actingAs($user);
        $this->getJson('/api/v1/me')->assertJsonPath('data.phone', '0793334444')->assertJsonPath('data.email', $user->email);

        Sanctum::actingAs($this->makeAdmin());
        $this->getJson("/api/v1/properties/{$property->id}/comments")
            ->assertJsonPath('data.0.author.phone', '0793334444');

        Sanctum::actingAs($this->makeUser());
        $this->getJson("/api/v1/properties/{$property->id}/comments")
            ->assertJsonMissingPath('data.0.author.phone');
    }

    public function test_only_dealers_have_a_public_profile_page(): void
    {
        $regular = $this->makeUser(attrs: ['email' => 'private@example.com', 'phone' => '0790009999']);
        $dealer = $this->makeDealer();

        $this->get("/Dealers/{$regular->id}")->assertNotFound();
        $this->get("/Dealers/{$dealer->id}")->assertOk();
    }

    // ---------- logout is not a link ----------

    public function test_logout_requires_post(): void
    {
        $this->actingAs($this->makeUser());

        $this->get('/logout')->assertStatus(405);
        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect();
        $this->assertGuest();
    }

    // ---------- one password policy ----------

    public static function weakPasswords(): array
    {
        return [
            'no digit' => ['abcdefgh'],
            'no letter' => ['12345678'],
            'too short' => ['abc12'],
        ];
    }

    /** @dataProvider weakPasswords */
    public function test_weak_passwords_are_refused_on_api_registration(string $password): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'N', 'email' => 'n@example.com', 'password' => $password, 'password_confirmation' => $password,
        ])->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    /** @dataProvider weakPasswords */
    public function test_weak_passwords_are_refused_on_website_signup(string $password): void
    {
        $this->post('/signup', [
            'name' => 'N', 'username' => 'nn', 'email' => 'n@example.com', 'phone' => '0791230000',
            'password' => $password, 'password_confirmation' => $password,
        ])->assertSessionHasErrors('password');
    }

    public function test_weak_passwords_are_refused_when_changing_or_resetting(): void
    {
        $user = $this->makeUser(attrs: ['password' => 'original-pass1']);

        Sanctum::actingAs($user);
        $this->putJson('/api/v1/me/password', [
            'current_password' => 'original-pass1', 'password' => 'allletters', 'password_confirmation' => 'allletters',
        ])->assertUnprocessable()->assertJsonValidationErrors('password');

        $this->post('/reset-password', [
            'token' => 'x', 'email' => $user->email, 'password' => '12345678', 'password_confirmation' => '12345678',
        ])->assertSessionHasErrors('password');
    }

    // ---------- brute force ----------

    public function test_repeated_wrong_passwords_for_one_account_are_throttled_on_the_api(): void
    {
        $this->makeUser(attrs: ['email' => 'target@example.com', 'password' => 'right-pass1']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', ['email' => 'target@example.com', 'password' => "wrong{$i}"])
                ->assertUnprocessable();
        }

        // even the CORRECT password is refused once the account is locked for the window
        $this->postJson('/api/v1/auth/login', ['email' => 'target@example.com', 'password' => 'right-pass1'])
            ->assertStatus(429);
    }

    public function test_throttling_one_account_does_not_lock_everybody_else_out(): void
    {
        $this->makeUser(attrs: ['email' => 'target@example.com', 'password' => 'right-pass1']);
        $this->makeUser(attrs: ['email' => 'other@example.com', 'password' => 'other-pass1']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', ['email' => 'target@example.com', 'password' => 'nope']);
        }

        $this->postJson('/api/v1/auth/login', ['email' => 'other@example.com', 'password' => 'other-pass1'])->assertOk();
    }

    public function test_the_website_login_is_throttled_per_account_too(): void
    {
        $this->makeUser(attrs: ['email' => 'web@example.com', 'password' => 'right-pass1']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'web@example.com', 'password' => 'nope'])->assertSessionHasErrors('email');
        }

        $this->post('/login', ['email' => 'web@example.com', 'password' => 'right-pass1'])->assertStatus(429);
        $this->assertGuest();
    }

    // ---------- tokens ----------

    public function test_api_tokens_expire(): void
    {
        $this->assertSame(60 * 24 * 30, config('sanctum.expiration'));

        $user = $this->makeUser();
        $token = $user->createToken('phone')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/me')->assertOk();

        $this->travel(31)->days();
        $this->app['auth']->forgetGuards();

        $this->withToken($token)->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_changing_the_password_signs_other_devices_out_but_not_this_one(): void
    {
        $user = $this->makeUser(attrs: ['password' => 'original-pass1']);
        $current = $user->createToken('this phone')->plainTextToken;
        $user->createToken('stolen phone');

        $this->withToken($current)->putJson('/api/v1/me/password', [
            'current_password' => 'original-pass1', 'password' => 'brand-new-pass2', 'password_confirmation' => 'brand-new-pass2',
        ])->assertOk();

        $this->assertSame(1, $user->tokens()->count());
        $this->assertTrue(Hash::check('brand-new-pass2', $user->fresh()->password));
    }

    public function test_changing_the_password_on_the_website_revokes_api_tokens(): void
    {
        $user = $this->makeUser(attrs: ['password' => 'original-pass1']);
        $user->createToken('phone');

        $this->actingAs($user)->post('/UpdatePassword', ['password' => 'original-pass1', 'newPassword' => 'brand-new-pass2'])
            ->assertSessionHas('PasswordUpdated');

        $this->assertSame(0, $user->tokens()->count());
    }

    // ---------- headers / CORS ----------

    public function test_responses_send_a_content_security_policy_that_blocks_framing_and_base_hijacking(): void
    {
        $csp = $this->get('/login')->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        $this->assertStringContainsString("base-uri 'self'", $csp);
        $this->assertStringContainsString("form-action 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
    }

    public function test_signed_in_pages_are_not_cacheable_by_shared_caches(): void
    {
        $this->actingAs($this->makeUser());

        $cache = $this->get('/Profile')->headers->get('Cache-Control');

        $this->assertStringContainsString('no-store', $cache);
        $this->assertStringContainsString('private', $cache);
    }

    public function test_other_websites_may_not_call_the_api_from_a_browser(): void
    {
        $response = $this->getJson('/api/v1/properties', ['Origin' => 'https://evil.example']);

        $response->assertOk();
        $this->assertNull($response->headers->get('Access-Control-Allow-Origin'));
    }

    // ---------- social login ----------

    private function fakeProvider(string $name, SocialUser $user): void
    {
        $driver = Mockery::mock();
        // stateless() is NOT allowed: the OAuth state must be verified. Calling it would fail the test.
        $driver->shouldReceive('user')->andReturn($user);
        Socialite::shouldReceive('driver')->with($name)->andReturn($driver);
    }

    private function socialUser(string $id, string $email, array $raw = []): SocialUser
    {
        $user = (new SocialUser())->map(['id' => $id, 'name' => 'Social Person', 'email' => $email]);
        $user->user = $raw;

        return $user;
    }

    public function test_facebook_cannot_take_over_an_existing_password_account(): void
    {
        $victim = $this->makeUser(attrs: ['email' => 'victim@example.com', 'password' => 'victim-pass1']);
        $this->fakeProvider('facebook', $this->socialUser('attacker-fb-id', 'victim@example.com'));

        $this->get('/callback/oauth/facebook')->assertRedirect(route('login'))->assertSessionHas('error');

        $this->assertGuest();
        $this->assertNull($victim->fresh()->facebook_id);
    }

    public function test_an_unverified_google_email_cannot_take_over_an_account_either(): void
    {
        $victim = $this->makeUser(attrs: ['email' => 'victim@example.com']);
        $this->fakeProvider('google', $this->socialUser('g-1', 'victim@example.com', ['email_verified' => false]));

        $this->get('/callback/oauth/google')->assertSessionHas('error');

        $this->assertGuest();
        $this->assertNull($victim->fresh()->google_id);
    }

    public function test_a_verified_google_email_links_to_the_account_and_signs_in(): void
    {
        $user = $this->makeUser(attrs: ['email' => 'me@example.com']);
        $this->fakeProvider('google', $this->socialUser('g-42', 'me@example.com', ['email_verified' => true]));

        $this->get('/callback/oauth/google')->assertRedirect();

        $this->assertAuthenticatedAs($user);
        $this->assertSame('g-42', $user->fresh()->google_id);
    }

    public function test_social_signup_creates_a_regular_user_never_an_admin(): void
    {
        $this->fakeProvider('google', $this->socialUser('g-7', 'new@example.com', ['email_verified' => true]));

        $this->get('/callback/oauth/google')->assertRedirect();

        $created = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_USER, $created->role_id);
        $this->assertNotNull($created->email_verified_at);
        $this->assertAuthenticatedAs($created);
    }

    public function test_only_known_providers_are_accepted(): void
    {
        $this->get('/login/oauth/github')->assertNotFound();
        $this->get('/callback/oauth/github')->assertNotFound();
    }

    public function test_social_login_errors_do_not_leak_internal_details(): void
    {
        $driver = Mockery::mock();
        $driver->shouldReceive('user')->andThrow(new \RuntimeException('SQLSTATE[HY000] secret internals'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($driver);

        $response = $this->get('/callback/oauth/google');

        $response->assertRedirect(route('login'));
        $this->assertStringNotContainsString('SQLSTATE', (string) session('error'));
    }

    // ---------- uploads ----------

    private function pngClaiming(int $width, int $height): UploadedFile
    {
        $ihdr = pack('N', $width) . pack('N', $height) . "\x08\x02\x00\x00\x00";
        $chunk = pack('N', 13) . 'IHDR' . $ihdr . pack('N', crc32('IHDR' . $ihdr));
        $bytes = "\x89PNG\r\n\x1a\n" . $chunk . pack('N', 0) . 'IEND' . pack('N', crc32('IEND'));

        return UploadedFile::fake()->createWithContent('huge.png', $bytes);
    }

    public function test_absurdly_large_images_are_refused(): void
    {
        $dealer = $this->makeDealer();
        Sanctum::actingAs($dealer);

        $this->post('/api/v1/properties', $this->propertyPayload() + ['images' => [$this->pngClaiming(7000, 10)]], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('images.0');

        $this->post('/api/v1/me/photo', ['photo' => $this->pngClaiming(10, 7000)], ['Accept' => 'application/json'])
            ->assertUnprocessable();
    }

    // ---------- accountability ----------

    public function test_destructive_admin_actions_are_logged_with_who_did_them(): void
    {
        Log::spy();
        $admin = $this->makeAdmin();
        $victim = $this->makeUser();

        $this->actingAs($admin)->delete("/admin/ManageUsers/{$victim->id}")->assertRedirect();

        Log::shouldHaveReceived('notice')->withArgs(
            fn ($message, $context) => $message === 'admin.user.deleted'
                && $context['admin_id'] === $admin->id
                && $context['user_id'] === $victim->id
        )->once();
    }
}
