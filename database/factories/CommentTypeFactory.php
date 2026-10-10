<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\comment;

class CommentTypeFactory extends Factory
{
    
    public function definition(): array
    {
        return [
            'name'=>fake()->unique()->randomElement(['Ananyomuys','Normal']),
        ];
    }
}
