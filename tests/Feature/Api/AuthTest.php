<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\MakesMarketplaceData;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, MakesMarketplaceData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedLookups();
    }

    private function registration(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Sara Test',
            'email' => 'sara@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
    }

    public function test_register_creates_a_regular_user_and_returns_a_token(): void
    {
        $this->postJson('/api/v1/auth/register', $this->registration())
            ->assertCreated()
            ->assertJsonPath('user.email', 'sara@example.com')
            ->assertJsonPath('user.role_id', User::ROLE_USER)
            ->assertJsonStructure(['token']);
    }

    public function test_register_cannot_choose_a_role(): void
    {
        $this->postJson('/api/v1/auth/register', $this->registration(['role_id' => User::ROLE_ADMIN]))
            ->assertCreated()
            ->assertJsonPath('user.role_id', User::ROLE_USER);
    }

    public function test_register_validates_input(): void
    {
        $this->postJson('/api/v1/auth/register', ['name' => '', 'email' => 'nope', 'password' => 'short'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $this->makeUser(attrs: ['email' => 'sara@example.com']);

        $this->postJson('/api/v1/auth/register', $this->registration())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_two_people_can_share_a_display_name(): void
    {
        $this->makeUser(attrs: ['name' => 'Sara Test']);

        $this->postJson('/api/v1/auth/register', $this->registration())->assertCreated();
    }

    public function test_login_with_valid_credentials(): void
    {
        $this->makeUser(attrs: ['email' => 'a@example.com', 'password' => 'secret-pass']);

        $this->postJson('/api/v1/auth/login', ['email' => 'a@example.com', 'password' => 'secret-pass'])
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name']]);
    }

    public function test_login_with_wrong_password_fails(): void
    {
        $this->makeUser(attrs: ['email' => 'a@example.com', 'password' => 'secret-pass']);

        $this->postJson('/api/v1/auth/login', ['email' => 'a@example.com', 'password' => 'wrong'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_me_requires_authentication_and_returns_json_without_accept_header(): void
    {
        $this->get('/api/v1/me')->assertUnauthorized()->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_me_returns_the_current_user_with_email(): void
    {
        Sanctum::actingAs($user = $this->makeUser());

        $this->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_other_users_emails_are_not_exposed(): void
    {
        $dealer = $this->makeDealer();
        $property = $this->makeProperty($dealer);
        Sanctum::actingAs($this->makeUser());

        $this->getJson("/api/v1/properties/{$property->id}")
            ->assertOk()
            ->assertJsonMissingPath('data.dealer.email');
    }

    public function test_logout_revokes_the_token(): void
    {
        $token = $this->makeUser()->createToken('t')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();

        $this->app['auth']->forgetGuards(); // the test app instance caches the resolved user
        $this->withToken($token)->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_profile_can_be_updated_but_not_the_role(): void
    {
        Sanctum::actingAs($user = $this->makeUser());

        $this->putJson('/api/v1/me', ['name' => 'New Name', 'phone' => '0791234567', 'role_id' => 3])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name');

        $this->assertSame(User::ROLE_USER, $user->fresh()->role_id);
    }

    public function test_password_change_requires_the_current_password(): void
    {
        Sanctum::actingAs($this->makeUser(attrs: ['password' => 'old-password']));

        $this->putJson('/api/v1/me/password', [
            'current_password' => 'wrong', 'password' => 'new-password1', 'password_confirmation' => 'new-password1',
        ])->assertUnprocessable()->assertJsonValidationErrors('current_password');

        $this->putJson('/api/v1/me/password', [
            'current_password' => 'old-password', 'password' => 'new-password1', 'password_confirmation' => 'new-password1',
        ])->assertOk();
    }
}
