<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyImageFactory extends Factory
{
   
    public function definition(): array
    {
        return [
            'image_path'=>fake()->imageUrl(),
            'position'=>function(array $attributes)
            {
                return Property::find($attributes['Property_id'])->images()->count()+1;
            }
        ];
    }
}
