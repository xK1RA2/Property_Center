<?php

namespace Tests\Feature\Api;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\MakesMarketplaceData;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase, MakesMarketplaceData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedLookups();
    }

    // ---------- public listing ----------

    public function test_listing_shows_only_available_published_properties(): void
    {
        $dealer = $this->makeDealer();
        $visible = $this->makeProperty($dealer);
        $this->makeProperty($dealer, ['status' => 'Not Available']);
        $this->makeProperty($dealer, ['published_at' => now()->addWeek()]);

        $this->getJson('/api/v1/properties')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $visible->id);
    }

    public function test_listing_is_paginated_and_filterable(): void
    {
        $dealer = $this->makeDealer();
        $this->makeProperty($dealer, ['PurchaseType' => 'Rent', 'price' => 500]);
        $this->makeProperty($dealer, ['PurchaseType' => 'Sell', 'price' => 900000]);
        $this->makeProperty($dealer, ['PurchaseType' => 'Sell', 'price' => 100000, 'address' => 'Unique Boulevard 7']);

        $this->getJson('/api/v1/properties?purchase_type=Rent')->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/properties?min_price=200000')->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/properties?search=Unique')->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/properties?per_page=2')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3);
    }

    public function test_listing_can_be_sorted_by_price(): void
    {
        $dealer = $this->makeDealer();
        $this->makeProperty($dealer, ['price' => 300]);
        $this->makeProperty($dealer, ['price' => 100]);
        $this->makeProperty($dealer, ['price' => 200]);

        $this->getJson('/api/v1/properties?sort=price_asc')
            ->assertJsonPath('data.0.price', 100)
            ->assertJsonPath('data.2.price', 300);
    }

    public function test_listing_rejects_bad_filters(): void
    {
        $this->getJson('/api/v1/properties?purchase_type=Gift&per_page=500')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['purchase_type', 'per_page']);
    }

    public function test_show_returns_full_detail_with_absolute_image_urls(): void
    {
        $property = $this->makeProperty($this->makeDealer());
        $property->PropertyImages()->create(['image_path' => 'images/a.jpg', 'position' => 1]);

        $this->getJson("/api/v1/properties/{$property->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $property->id)
            ->assertJsonPath('data.city.name', 'Abdoun')
            ->assertJsonPath('data.type.name', 'Apartment')
            ->assertJsonStructure(['data' => ['features', 'images', 'dealer', 'location']])
            ->assertJson(fn ($json) => $json->where('data.images.0.url', fn ($u) => str_starts_with($u, 'http'))->etc());
    }

    public function test_unknown_property_is_404(): void
    {
        $this->getJson('/api/v1/properties/999999')->assertNotFound();
    }

    public function test_show_includes_favorite_and_rating_for_a_signed_in_viewer_only(): void
    {
        $property = $this->makeProperty($this->makeDealer());
        $viewer = $this->makeUser();
        $viewer->Favourite_Property()->attach($property->id);

        $this->getJson("/api/v1/properties/{$property->id}")
            ->assertJsonMissingPath('data.is_favorite');

        Sanctum::actingAs($viewer);
        $this->getJson("/api/v1/properties/{$property->id}")
            ->assertJsonPath('data.is_favorite', true);
    }

    // ---------- create ----------

    public function test_dealer_can_create_a_property_with_features_location_and_photos(): void
    {
        Storage::fake('public');
        $dealer = $this->makeDealer();
        Sanctum::actingAs($dealer);

        $payload = $this->propertyPayload() + [
            'images' => [$this->fakePng('a.jpg'), $this->fakePng('b.jpg')],
        ];

        $response = $this->post('/api/v1/properties', $payload, ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.price', 750)
            ->assertJsonPath('data.purchase_type', 'Rent')
            ->assertJsonPath('data.dealer.id', $dealer->id)
            ->assertJsonPath('data.features.Bedrooms', 3)
            ->assertJsonPath('data.location.lat', 31.95)
            ->assertJsonCount(2, 'data.images');

        $this->assertDatabaseHas('properties', ['id' => $response->json('data.id'), 'Dealer_id' => $dealer->id]);
        Storage::disk('public')->assertExists($response->json('data.images.0.url') ? 'images/' . basename($response->json('data.images.0.url')) : '');
    }

    public function test_create_validates_input(): void
    {
        Sanctum::actingAs($this->makeDealer());

        $this->postJson('/api/v1/properties', ['price' => -5, 'purchase_type' => 'Gift'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['purchase_type', 'property_type_id', 'city_id', 'price', 'year', 'address']);
    }

    public function test_regular_users_and_guests_cannot_create_properties(): void
    {
        $this->postJson('/api/v1/properties', $this->propertyPayload())->assertUnauthorized();

        Sanctum::actingAs($this->makeUser());
        $this->postJson('/api/v1/properties', $this->propertyPayload())->assertForbidden();
    }

    // ---------- update / delete ----------

    public function test_owner_can_update_and_unspecified_fields_are_untouched(): void
    {
        $dealer = $this->makeDealer();
        $property = $this->makeProperty($dealer, ['address' => 'Keep Me 1', 'year' => 2001]);
        Sanctum::actingAs($dealer);

        $this->patchJson("/api/v1/properties/{$property->id}", ['price' => 555, 'features' => ['Bedrooms' => 6]])
            ->assertOk()
            ->assertJsonPath('data.price', 555)
            ->assertJsonPath('data.features.Bedrooms', 6);

        $fresh = $property->fresh();
        $this->assertSame('Keep Me 1', $fresh->address);
        $this->assertSame(2001, $fresh->year);
        $this->assertEquals($dealer->id, $fresh->Dealer_id);
    }

    public function test_multipart_update_through_method_spoofing(): void
    {
        Storage::fake('public');
        $dealer = $this->makeDealer();
        $property = $this->makeProperty($dealer);
        Sanctum::actingAs($dealer);

        $this->post("/api/v1/properties/{$property->id}", [
            '_method' => 'PATCH',
            'price' => 4321,
            'images' => [$this->fakePng('new.jpg')],
        ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.price', 4321)
            ->assertJsonCount(1, 'data.images');
    }

    public function test_another_dealer_cannot_update_or_delete(): void
    {
        $property = $this->makeProperty($this->makeDealer());
        Sanctum::actingAs($this->makeDealer());

        $this->patchJson("/api/v1/properties/{$property->id}", ['price' => 1])->assertForbidden();
        $this->deleteJson("/api/v1/properties/{$property->id}")->assertForbidden();
        $this->assertSame(100000, $property->fresh()->price);
    }

    public function test_regular_user_cannot_update_or_delete(): void
    {
        $property = $this->makeProperty($this->makeDealer());
        Sanctum::actingAs($this->makeUser());

        $this->patchJson("/api/v1/properties/{$property->id}", ['price' => 1])->assertForbidden();
        $this->deleteJson("/api/v1/properties/{$property->id}")->assertForbidden();
    }

    public function test_admin_can_update_and_delete_any_property(): void
    {
        $property = $this->makeProperty($this->makeDealer());
        Sanctum::actingAs($this->makeAdmin());

        $this->patchJson("/api/v1/properties/{$property->id}", ['price' => 42])->assertOk();
        $this->deleteJson("/api/v1/properties/{$property->id}")->assertNoContent();

        $this->assertSoftDeleted('properties', ['id' => $property->id]);
        $this->getJson("/api/v1/properties/{$property->id}")->assertNotFound();
    }

    public function test_my_properties_lists_only_the_dealers_own_listings_of_any_status(): void
    {
        $dealer = $this->makeDealer();
        $mine = $this->makeProperty($dealer, ['status' => 'Not Available']);
        $this->makeProperty($this->makeDealer());
        Sanctum::actingAs($dealer);

        $this->getJson('/api/v1/my/properties')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $mine->id);
    }

    public function test_my_properties_is_dealer_only(): void
    {
        Sanctum::actingAs($this->makeUser());

        $this->getJson('/api/v1/my/properties')->assertForbidden();
    }
}
