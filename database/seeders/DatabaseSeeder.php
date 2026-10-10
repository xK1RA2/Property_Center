<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ReferenceDataSeeder::class);

        User::factory()
            ->count(3)
            ->state(['role_id' => User::ROLE_USER])
            ->create();

        User::factory()
            ->count(4)
            ->state(['role_id' => User::ROLE_DEALER])
            ->has(
                Property::factory()
                    ->count(2)
                    ->has(
                        PropertyImage::factory()
                            ->count(5)
                            ->sequence(fn (Sequence $sequence) => ['position' => $sequence->index % 5 + 1]),
                        'PropertyImages'
                    )
                    ->hasFeatures()
                    ->hasLocation()
            )
            ->create();
    }
}
