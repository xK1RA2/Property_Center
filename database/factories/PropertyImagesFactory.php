<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;


class PropertyImagesFactory extends Factory
{
   
    public function definition(): array
    {
        return [
            'image_path'=>fake()->imageUrl(),//randome imageUrl
            'position'=>function(array $attributes)
            {
                return Property::find($attributes['Property_id'])->images()->count()+1;
            }
        ];
    }
}
