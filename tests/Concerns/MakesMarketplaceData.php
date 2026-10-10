<?php

namespace Tests\Concerns;

use App\Models\City;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

trait MakesMarketplaceData
{
    protected PropertyType $type;
    protected City $city;

    protected function seedLookups(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'user'],
            ['id' => 2, 'name' => 'dealer'],
            ['id' => 3, 'name' => 'admin'],
        ]);
        DB::table('comment_types')->insert([
            ['id' => 1, 'name' => 'Normal'],
            ['id' => 2, 'name' => 'Ananyomus'],
        ]);

        $this->type = PropertyType::create(['name' => 'Apartment']);
        $state = State::create(['name' => 'Amman']);
        $this->city = $state->cities()->create(['name' => 'Abdoun']);
    }

    protected function makeUser(int $role = User::ROLE_USER, array $attrs = []): User
    {
        return User::factory()->create(['role_id' => $role] + $attrs);
    }

    protected function makeDealer(array $attrs = []): User
    {
        return $this->makeUser(User::ROLE_DEALER, $attrs);
    }

    protected function makeAdmin(array $attrs = []): User
    {
        return $this->makeUser(User::ROLE_ADMIN, $attrs);
    }

    protected function makeProperty(User $dealer, array $attrs = []): Property
    {
        $property = Property::create($attrs + [
            'Dealer_id' => $dealer->id,
            'PurchaseType' => 'Sell',
            'property_type_id' => $this->type->id,
            'city_id' => $this->city->id,
            'year' => 2020,
            'price' => 100000,
            'address' => '1 Test Street',
            'description' => 'A test property',
            'status' => 'Available',
        ]);
        $property->forceFill(['published_at' => $attrs['published_at'] ?? now()->subDay()])->save();
        $property->Features()->create([]);

        return $property;
    }

    protected function fakePng(string $name = 'photo.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')
        );
    }

    protected function propertyPayload(array $overrides = []): array
    {
        return $overrides + [
            'purchase_type' => 'Rent',
            'property_type_id' => $this->type->id,
            'city_id' => $this->city->id,
            'price' => 750,
            'year' => 2022,
            'address' => '5 Payload Road',
            'description' => 'Created through the API',
            'features' => ['Bedrooms' => 3, 'Bathrooms' => 2, 'Area' => 120, 'Parking' => true],
            'location' => ['name' => 'Home', 'lat' => 31.95, 'lng' => 35.91],
        ];
    }
}
