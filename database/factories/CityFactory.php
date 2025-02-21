<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;


class CityFactory extends Factory
{
 
    public function definition(): array
    {
        return [
            'name'=>fake()->city(),  
            'states_id' => State::inRandomOrder()->value('id') ?? State::factory()->create()->id,

        ];
    }
}
