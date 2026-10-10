<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'Dealer_id' => fn () => User::where('role_id', User::ROLE_DEALER)->inRandomOrder()->value('id')
                ?? User::factory()->create(['role_id' => User::ROLE_DEALER])->id,
            'property_type_id' => fn () => PropertyType::inRandomOrder()->value('id')
                ?? PropertyType::factory()->create()->id,
            'city_id' => fn () => City::inRandomOrder()->value('id')
                ?? City::factory()->create()->id,
            'PurchaseType' => $this->faker->randomElement(['Sell', 'Rent']),
            'year' => $this->faker->numberBetween(1980, (int) date('Y')),
            'price' => $this->faker->numberBetween(5, 100) * 1000,
            'address' => $this->faker->address(),
            'description' => $this->faker->text(500),
            'status' => $this->faker->randomElement(['Available', 'Not Available']),
        ];
    }

    public function listed(): static
    {
        return $this->state(fn () => ['status' => 'Available'])
            ->afterCreating(fn (Property $p) => $p->forceFill(['published_at' => now()->subDay()])->save());
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Property $property) {
            if ($property->published_at === null && $this->faker->boolean(90)) {
                $property->forceFill([
                    'published_at' => $this->faker->dateTimeBetween('-1 month', '-1 hour'),
                ])->save();
            }
        });
    }
}
