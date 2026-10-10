<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CommentType;
use App\Models\PropertyType;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** What an operator runs on a brand new production database. */
class ProductionSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_reference_data_seeder_creates_what_the_app_needs_with_stable_ids(): void
    {
        $this->seed(ReferenceDataSeeder::class);

        $this->assertSame(['user', 'dealer', 'admin'], Role::orderBy('id')->pluck('name')->all());
        $this->assertSame(['Normal', 'Ananyomus'], CommentType::orderBy('id')->pluck('name')->all());
        $this->assertGreaterThan(0, PropertyType::count());
        $this->assertSame(100, City::count());
        $this->assertSame(0, User::count(), 'production seeding must not create fake users');
    }

    public function test_reference_data_seeder_is_idempotent(): void
    {
        $this->seed(ReferenceDataSeeder::class);
        $counts = [Role::count(), City::count(), PropertyType::count()];

        $this->seed(ReferenceDataSeeder::class);

        $this->assertSame($counts, [Role::count(), City::count(), PropertyType::count()]);
    }

    public function test_create_admin_command_makes_a_working_admin(): void
    {
        $this->seed(ReferenceDataSeeder::class);

        $this->artisan('app:create-admin', ['email' => 'boss@example.com', '--password' => 'long-enough-1', '--name' => 'Boss'])
            ->assertSuccessful();

        $admin = User::where('email', 'boss@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_ADMIN, $admin->role_id);
        $this->assertTrue(Hash::check('long-enough-1', $admin->password));

        $this->postJson('/api/v1/auth/login', ['email' => 'boss@example.com', 'password' => 'long-enough-1'])
            ->assertOk()
            ->assertJsonPath('user.role_id', User::ROLE_ADMIN);
    }

    public function test_create_admin_promotes_an_existing_user(): void
    {
        $this->seed(ReferenceDataSeeder::class);
        $user = User::factory()->create(['role_id' => User::ROLE_USER, 'email' => 'dev@example.com']);

        $this->artisan('app:create-admin', ['email' => 'dev@example.com', '--password' => 'long-enough-1'])
            ->assertSuccessful();

        $this->assertSame(User::ROLE_ADMIN, $user->fresh()->role_id);
        $this->assertSame(1, User::count());
    }

    public function test_create_admin_rejects_weak_passwords_and_bad_emails(): void
    {
        $this->seed(ReferenceDataSeeder::class);

        $this->artisan('app:create-admin', ['email' => 'boss@example.com', '--password' => 'short'])->assertFailed();
        $this->artisan('app:create-admin', ['email' => 'not-an-email', '--password' => 'long-enough-1'])->assertFailed();

        $this->assertSame(0, User::count());
    }
}
