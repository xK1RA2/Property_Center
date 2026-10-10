<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

/**
 * The database stores role ids and the seeders/migrations/mobile app all assume them.
 * Changing a number here silently changes who can do what, so it is pinned.
 */
class RoleContractTest extends TestCase
{
    public function test_role_ids_are_stable(): void
    {
        $this->assertSame(1, User::ROLE_USER);
        $this->assertSame(2, User::ROLE_DEALER);
        $this->assertSame(3, User::ROLE_ADMIN);
    }

    public function test_the_reference_seeder_writes_the_same_ids(): void
    {
        $source = file_get_contents(__DIR__ . '/../../database/seeders/ReferenceDataSeeder.php');

        $this->assertStringContainsString("['id' => 1, 'name' => 'user']", $source);
        $this->assertStringContainsString("['id' => 2, 'name' => 'dealer']", $source);
        $this->assertStringContainsString("['id' => 3, 'name' => 'admin']", $source);
    }
}
