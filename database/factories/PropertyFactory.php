<?php

namespace Database\Factories;


use App\Models\PropertyType;
use App\Models\Property;
use App\Models\City;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class PropertyFactory extends Factory
{
   
   
    public function definition(): array
    {
        $dealerRole = Role::where('name','dealer');
        return [
            'PurchaseType' => $this->faker->randomElement(['Sell', 'Rent']),
            'Property_type_id'=> PropertyType::inRandomOrder()->value('id')?? PropertyType::factory()->create()->id,
            'dealer_id' => User::whereHas('role', function ($query) {
                $query->where('name', 'dealer');
                })->inRandomOrder()->value('id') ?? User::factory()->create()->id,
            'city_id'=> City::inRandomOrder()->value('id')??City::factory()->create()->id,
            'year'=>fake()->year(), 
            'price'=>((int)fake()->randomFloat(2,5,100)) * 1000 ,
            
            'address'=>fake()->address(),
            'phone'=>function(array $attributes)
            {
                return User::find($attributes['dealer_id'])->phone ??null;
            },
           
            'description'=>fake()->text(2000),
            'published_at'=>fake()->optional(0.9)
          
            ->dateTimeBetween('-1 month','+1 day'),
                'status' => fake()->randomElement(['Available', 'Not Available'])

        ];
    }
}
