<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class OrderFactory extends Factory
{
   
    public function definition(): array
    {
        $role = Role::where('name', 'user')->first(); 
        return [
                'user_id' => User::factory()->create([
                'role_id' => $role->id,
            ])->id, 
            'Property_id' => Property::factory(), 
            'status' => $this->faker->randomElement(['pending', 'completed', 'canceled']), 
            'price' => $this->faker->randomFloat(2, 10000, 50000), 
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
