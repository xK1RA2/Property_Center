<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Checkout;
use App\Models\Comment;
use App\Models\Property;
use App\Models\Rating;
use App\Models\TraderRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesMarketplaceData;
use Tests\TestCase;

/**
 * Renders every screen of the website with realistic data (not empty tables),
 * so a template that breaks on a relation or a null shows up as a failing test.
 */
class FeatureSweepTest extends TestCase
{
    use RefreshDatabase, MakesMarketplaceData;

    private User $dealer;
    private User $buyer;
    private User $admin;
    private Property $property;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedLookups();

        $this->dealer = $this->makeDealer(['phone' => '0791000001', 'username' => 'dealer_one']);
        $this->buyer = $this->makeUser(attrs: ['phone' => '0791000002', 'username' => 'buyer_one']);
        $this->admin = $this->makeAdmin();

        $this->property = $this->makeProperty($this->dealer, ['PurchaseType' => 'Rent', 'description' => 'Sunny flat']);
        $this->property->PropertyImages()->create(['image_path' => 'images/a.jpg', 'position' => 1]);
        $this->property->PropertyImages()->create(['image_path' => 'https://example.com/b.jpg', 'position' => 2]);
        $this->property->Features()->update(['Bedrooms' => 3, 'Bathrooms' => 2, 'Area' => 120, 'Parking' => 1]);
        $this->property->location()->create(['name' => 'Home', 'lat' => 31.95, 'lng' => 35.91]);

        Comment::create(['user_id' => $this->buyer->id, 'Property_id' => $this->property->id, 'comment_type_id' => 1, 'Description' => 'Public comment']);
        Comment::create(['user_id' => $this->buyer->id, 'Property_id' => $this->property->id, 'comment_type_id' => 2, 'Description' => 'Hidden comment']);
        Rating::create(['user_id' => $this->buyer->id, 'Property_id' => $this->property->id, 'Rate' => 4]);
        Rating::create(['user_id' => $this->admin->id, 'Property_id' => $this->property->id, 'Rate' => 5]);

        Booking::create([
            'user_id' => $this->buyer->id, 'dealer_id' => $this->dealer->id, 'property_id' => $this->property->id,
            'date' => now()->addDay(), 'description' => 'Visit please', 'status' => 'Pending',
        ]);
        Checkout::create([
            'user_id' => $this->buyer->id, 'dealer_id' => $this->dealer->id, 'property_id' => $this->property->id,
            'price' => 500, 'card_last4' => '4242', 'address' => 'Somewhere', 'date' => now(),
        ]);
        $this->buyer->Favourite_Property()->attach($this->property->id);
        TraderRequest::create(['user_id' => $this->buyer->id]);
    }

    public function test_public_screens_with_data(): void
    {
        foreach (['/index', '/View', '/Dealers', '/Maps', '/login', '/signup', '/forgot'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get("/Details/{$this->property->id}")
            ->assertOk()
            ->assertSee('Public comment')
            ->assertSee('Hidden comment'); // the text shows, the author does not (checked in Api tests)

        $this->get("/Dealers/{$this->dealer->id}")->assertOk();
        $this->get('/Search?Purchase_Type=Rent&price_from=1&price_to=999999&sort=-price')->assertOk();
    }

    public function test_search_page_shows_matching_listing(): void
    {
        $this->get('/Search?Purchase_Type=Rent&city_id=' . $this->city->id)
            ->assertOk()
            ->assertViewHas('Properties', fn ($p) => $p->contains('id', $this->property->id));
    }

    public function test_buyer_screens_with_data(): void
    {
        $this->actingAs($this->buyer);

        foreach (['/index', '/Profile', '/orders', '/wishList', "/Details/{$this->property->id}", '/View'] as $url) {
            $this->get($url)->assertOk();
        }

        // the order list shows the property that was bought
        $this->get('/orders')->assertOk()->assertViewHas('orders', fn ($o) => $o->count() === 1);
    }

    public function test_dealer_screens_with_data(): void
    {
        $this->actingAs($this->dealer);

        foreach (['/dashboard', '/Dealer/dashboard', '/myProperties', '/property', '/locations', '/Profile'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get("/property/{$this->property->id}/edit")->assertOk();

        $this->get('/dashboard')->assertViewHas('Requests', fn ($r) => $r->count() === 1)
            ->assertViewHas('Checkouts', fn ($c) => $c->count() === 1);
    }

    public function test_admin_screens_with_data(): void
    {
        $this->actingAs($this->admin);

        foreach (['/admin', '/ManageProperty', '/admin/ManageUsers', '/admin/ManageRequests', "/admin/UpdateProperty/{$this->property->id}"] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get("/admin/EditProperty/{$this->property->id}")->assertRedirect(route('UpdateProperty', $this->property));
    }

    public function test_admin_can_edit_a_listing_through_the_admin_form(): void
    {
        $this->actingAs($this->admin);

        $this->post("/property/update/{$this->property->id}", [
            'PropertyPurchase' => 'Sell',
            'PropertyName' => $this->type->id,
            'city_id' => $this->city->id,
            'price' => 123456,
            'Description' => 'Edited by admin',
        ])->assertRedirect(route('ManageProperty', $this->property));

        $fresh = $this->property->fresh();
        $this->assertSame(123456, $fresh->price);
        $this->assertSame('Edited by admin', $fresh->description);
        $this->assertSame('Sell', $fresh->PurchaseType);
        $this->assertEquals($this->dealer->id, $fresh->Dealer_id, 'an admin edit must not steal the listing');
    }

    public function test_admin_can_accept_a_dealer_request_and_the_user_can_then_list(): void
    {
        $this->actingAs($this->admin);
        $request = TraderRequest::first();

        $this->post('/admin/RequestAction', ['Action' => 'Accept', 'Request_id' => $request->id])
            ->assertRedirect(route('ManageRequests'));

        $this->assertSame(User::ROLE_DEALER, $this->buyer->fresh()->role_id);

        $this->actingAs($this->buyer->fresh())->get('/property')->assertOk();
    }

    public function test_admin_can_remove_a_listing_and_it_disappears_from_the_site(): void
    {
        $this->actingAs($this->admin);

        $this->delete("/admin/ManageProperty/{$this->property->id}")->assertRedirect(route('ManageProperty'));

        $this->get('/Search?Purchase_Type=Rent')
            ->assertViewHas('Properties', fn ($p) => !$p->contains('id', $this->property->id));
        $this->get('/index')->assertOk();
    }

    public function test_full_buying_journey_through_the_website(): void
    {
        $fresh = $this->makeUser(attrs: ['phone' => '0791000009', 'username' => 'fresh_one']);
        $property = $this->makeProperty($this->dealer, ['PurchaseType' => 'Sell']);
        $this->actingAs($fresh);

        $this->post("/wishList/{$property->id}")->assertRedirect();
        $this->post("/property/{$property->id}/rating", ['rating' => 5])->assertRedirect();
        $this->post("/Comments/{$property->id}", ['Description' => 'Interested'])->assertRedirect();
        $this->post("/Booking/{$property->id}", ['date' => now()->addDays(2)->toDateTimeString()])->assertRedirect();

        // the dealer sees the request and approves it
        $booking = Booking::where('property_id', $property->id)->firstOrFail();
        $this->actingAs($this->dealer)->post("/BookAccept/{$booking->id}")->assertRedirect();
        $this->assertSame('Approved', $booking->fresh()->status);

        // the buyer pays
        $this->actingAs($fresh)->post("/checkout/{$property->id}", ['card_last4' => '4242', 'address' => '1 Buyer St'])
            ->assertSessionHas('CheckoutSuccess');
        $this->assertSame('Not Available', $property->fresh()->status);

        $this->get('/orders')->assertOk()->assertViewHas('orders', fn ($o) => $o->count() === 1);
        $this->get("/Details/{$property->id}")->assertOk();
    }

    public function test_a_listing_without_photos_or_features_still_opens(): void
    {
        $bare = Property::create([
            "Dealer_id" => $this->dealer->id, "PurchaseType" => "Sell", "property_type_id" => $this->type->id,
            "city_id" => $this->city->id, "year" => 2020, "price" => 1000, "address" => "No photos here", "status" => "Available",
        ]);
        $bare->forceFill(["published_at" => now()->subDay()])->save();

        $this->get("/Details/{$bare->id}")->assertOk();
        $this->get("/index")->assertOk();
        $this->get("/View")->assertOk();

        $this->actingAs($this->dealer);
        $this->get("/myProperties")->assertOk();
        $this->get("/property/{$bare->id}/edit")->assertOk();
    }
}
