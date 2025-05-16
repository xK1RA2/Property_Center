<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition()
    {
        return [
            'name' => $this->faker->city,
            'lat' => $this->faker->latitude,
            'lng' => $this->faker->longitude,
            'property_id' => null, // optional if linked to property
        ];
    }
}
