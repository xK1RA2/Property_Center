<?php
namespace Database\Factories;

use App\Models\PropertyType;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyTypeFactory extends Factory
{
  
    protected $table ='Property_type';
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Apartment', 'Building','House','Villa','Office']),
            'type' => $this->faker->randomElement(['Sell', 'Rent']),
           ];
    }
}
