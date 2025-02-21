<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class PropertyFeaturesFactory extends Factory
{
    public function definition(): array
    {
        return [
        'Bedrooms'=>fake()->numberBetween(1,5),
        'Bathrooms'=>fake()->numberBetween(1,5),
        'Kitchen'=>fake()->numberBetween(1,5),
        'Rooms'=>fake()->numberBetween(1,5),
        'Heating'=>fake()->boolean(),
        'Air_Conditioner'=>fake()->boolean(),
        'Parking'=>fake()->boolean(),
        'Area'=>fake()->numberBetween(50,200),
        ];
    }
}
