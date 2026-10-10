<?php

namespace Tests\Feature\Api;

use App\Models\TraderRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\MakesMarketplaceData;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase, MakesMarketplaceData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedLookups();
    }

    public function test_admin_area_is_closed_to_everyone_but_admins(): void
    {
        $this->getJson('/api/v1/admin/users')->assertUnauthorized();

        foreach ([$this->makeUser(), $this->makeDealer()] as $nonAdmin) {
            Sanctum::actingAs($nonAdmin);
            $this->getJson('/api/v1/admin/users')->assertForbidden();
            $this->getJson('/api/v1/admin/trader-requests')->assertForbidden();
            $this->deleteJson('/api/v1/admin/users/' . $nonAdmin->id)->assertForbidden();
        }
    }

    public function test_admin_lists_users_with_roles(): void
    {
        $this->makeUser();
        Sanctum::actingAs($this->makeAdmin());

        $this->getJson('/api/v1/admin/users')
            ->assertOk()
            ->assertJsonPath('meta.total', 2)
            ->assertJsonStructure(['data' => [['id', 'name', 'role_id', 'role']]]);
    }

    public function test_admin_can_delete_a_regular_user(): void
    {
        $victim = $this->makeUser();
        Sanctum::actingAs($this->makeAdmin());

        $this->deleteJson("/api/v1/admin/users/{$victim->id}")->assertNoContent();
        $this->assertModelMissing($victim);
    }

    public function test_admin_cannot_delete_admins_or_themselves(): void
    {
        $admin = $this->makeAdmin();
        $otherAdmin = $this->makeAdmin();
        Sanctum::actingAs($admin);

        $this->deleteJson("/api/v1/admin/users/{$admin->id}")->assertUnprocessable();
        $this->deleteJson("/api/v1/admin/users/{$otherAdmin->id}")->assertUnprocessable();
    }

    public function test_dealer_with_listings_is_not_half_deleted(): void
    {
        $dealer = $this->makeDealer();
        $this->makeProperty($dealer);
        Sanctum::actingAs($this->makeAdmin());

        $this->deleteJson("/api/v1/admin/users/{$dealer->id}")
            ->assertUnprocessable()
            ->assertJsonPath('message', 'This dealer still has listings; remove them first.');
        $this->assertModelExists($dealer);
    }

    public function test_user_can_request_to_become_a_dealer_once(): void
    {
        $user = $this->makeUser();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/trader-requests')->assertCreated();
        $this->postJson('/api/v1/trader-requests')->assertOk();
        $this->assertSame(1, TraderRequest::where('user_id', $user->id)->count());
    }

    public function test_dealers_cannot_file_a_dealer_request(): void
    {
        Sanctum::actingAs($this->makeDealer());

        $this->postJson('/api/v1/trader-requests')->assertForbidden();
    }

    public function test_admin_approves_a_request_and_the_user_becomes_a_dealer(): void
    {
        $user = $this->makeUser();
        $request = TraderRequest::create(['user_id' => $user->id]);
        Sanctum::actingAs($this->makeAdmin());

        $this->getJson('/api/v1/admin/trader-requests')
            ->assertOk()
            ->assertJsonPath('data.0.user.id', $user->id);

        $this->patchJson("/api/v1/admin/trader-requests/{$request->id}", ['action' => 'approve'])->assertOk();

        $this->assertSame(User::ROLE_DEALER, $user->fresh()->role_id);
        $this->assertModelMissing($request);
    }

    public function test_admin_rejects_a_request_and_the_role_is_unchanged(): void
    {
        $user = $this->makeUser();
        $request = TraderRequest::create(['user_id' => $user->id]);
        Sanctum::actingAs($this->makeAdmin());

        $this->patchJson("/api/v1/admin/trader-requests/{$request->id}", ['action' => 'reject'])->assertOk();

        $this->assertSame(User::ROLE_USER, $user->fresh()->role_id);
        $this->assertModelMissing($request);
    }

    public function test_request_action_is_validated(): void
    {
        $request = TraderRequest::create(['user_id' => $this->makeUser()->id]);
        Sanctum::actingAs($this->makeAdmin());

        $this->patchJson("/api/v1/admin/trader-requests/{$request->id}", ['action' => 'explode'])->assertUnprocessable();
    }
}
