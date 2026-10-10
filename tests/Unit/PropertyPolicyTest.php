<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Models\User;
use App\Policies\PropertyPolicy;
use PHPUnit\Framework\TestCase;

class PropertyPolicyTest extends TestCase
{
    private PropertyPolicy $policy;

    protected function setUp(): void
    {
        $this->policy = new PropertyPolicy();
    }

    private function user(int $id, int $role): User
    {
        $user = new User();
        $user->id = $id;
        $user->role_id = $role;

        return $user;
    }

    private function propertyOf(int $dealerId): Property
    {
        $property = new Property();
        $property->Dealer_id = $dealerId;

        return $property;
    }

    public function test_the_owning_dealer_may_update_and_delete(): void
    {
        $dealer = $this->user(10, User::ROLE_DEALER);
        $property = $this->propertyOf(10);

        $this->assertTrue($this->policy->update($dealer, $property));
        $this->assertTrue($this->policy->delete($dealer, $property));
    }

    public function test_another_dealer_may_not(): void
    {
        $other = $this->user(11, User::ROLE_DEALER);
        $property = $this->propertyOf(10);

        $this->assertFalse($this->policy->update($other, $property));
        $this->assertFalse($this->policy->delete($other, $property));
    }

    public function test_a_regular_user_may_not_even_for_a_matching_id(): void
    {
        $user = $this->user(10, User::ROLE_USER);

        $this->assertFalse($this->policy->update($user, $this->propertyOf(10)));
    }

    public function test_an_admin_may_manage_any_listing(): void
    {
        $admin = $this->user(1, User::ROLE_ADMIN);
        $property = $this->propertyOf(99);

        $this->assertTrue($this->policy->update($admin, $property));
        $this->assertTrue($this->policy->delete($admin, $property));
    }
}
