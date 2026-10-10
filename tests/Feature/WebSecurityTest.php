<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Property;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MakesMarketplaceData;
use Tests\TestCase;

class WebSecurityTest extends TestCase
{
    use RefreshDatabase, MakesMarketplaceData;

    private User $dealer;
    private Property $property;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedLookups();
        $this->dealer = $this->makeDealer();
        $this->property = $this->makeProperty($this->dealer);
    }


    public function test_guests_are_sent_to_login_for_admin_pages(): void
    {
        foreach (['/admin', '/ManageProperty', '/admin/ManageUsers', '/admin/ManageRequests'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_non_admins_get_403_on_admin_pages(): void
    {
        foreach ([$this->makeUser(), $this->dealer] as $who) {
            $this->actingAs($who);
            foreach (['/admin', '/ManageProperty', '/admin/ManageUsers', '/admin/ManageRequests'] as $url) {
                $this->get($url)->assertForbidden();
            }
        }
    }

    public function test_admin_can_open_admin_pages(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->get('/admin/ManageUsers')->assertOk();
    }

    public function test_destructive_actions_are_not_reachable_with_get(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->get("/admin/ManageProperty/{$this->property->id}")->assertStatus(405);
        $this->get('/admin/ManageUsers/' . $this->makeUser()->id)->assertStatus(405);
        $this->assertNotSoftDeleted($this->property);
    }

    public function test_admin_deleting_a_user_deletes_that_user_only(): void
    {
        $admin = $this->makeAdmin();
        $victim = $this->makeUser();
        $bystander = $this->makeUser();
        $this->actingAs($admin);

        $this->delete("/admin/ManageUsers/{$victim->id}")->assertRedirect();

        $this->assertModelMissing($victim);
        $this->assertModelExists($bystander);
        $this->assertModelExists($admin);
    }

    public function test_non_admin_cannot_delete_users_or_properties(): void
    {
        $victim = $this->makeUser();
        $this->actingAs($this->makeDealer());

        $this->delete("/admin/ManageUsers/{$victim->id}")->assertForbidden();
        $this->delete("/admin/ManageProperty/{$this->property->id}")->assertForbidden();
        $this->assertModelExists($victim);
    }


    public function test_other_dealers_cannot_delete_or_update_my_property(): void
    {
        $this->actingAs($this->makeDealer());

        $this->post("/Destroy/{$this->property->id}")->assertForbidden();
        $this->post("/property/update/{$this->property->id}", [
            'PropertyPurchase' => 'Sell', 'PropertyName' => $this->type->id, 'city_id' => $this->city->id, 'price' => 1,
        ])->assertForbidden();
        $this->assertNotSoftDeleted($this->property);
    }

    public function test_dealer_cannot_decide_bookings_on_someone_elses_property(): void
    {
        $booking = Booking::create([
            'user_id' => $this->makeUser()->id, 'dealer_id' => $this->dealer->id, 'property_id' => $this->property->id,
            'date' => now()->addDay(), 'description' => 'x', 'status' => 'Pending',
        ]);

        $this->actingAs($this->makeDealer());
        $this->post("/BookAccept/{$booking->id}")->assertForbidden();
        $this->assertSame('Pending', $booking->fresh()->status);

        $this->actingAs($this->dealer);
        $this->post("/BookAccept/{$booking->id}")->assertRedirect();
        $this->assertSame('Approved', $booking->fresh()->status);
    }

    public function test_property_update_keeps_dealer_year_and_address(): void
    {
        $this->property->update(['year' => 1999, 'address' => 'Real Address 5']);
        $this->actingAs($this->dealer);

        $this->post("/property/update/{$this->property->id}", [
            'PropertyPurchase' => 'Rent', 'PropertyName' => $this->type->id, 'city_id' => $this->city->id, 'price' => 777,
        ])->assertRedirect();

        $fresh = $this->property->fresh();
        $this->assertSame(777, $fresh->price);
        $this->assertSame(1999, $fresh->year);
        $this->assertSame('Real Address 5', $fresh->address);
        $this->assertEquals($this->dealer->id, $fresh->Dealer_id);
    }


    public function test_booking_commenting_checkout_and_wishlist_reject_get(): void
    {
        $this->actingAs($this->makeUser());
        $id = $this->property->id;

        $this->get("/Booking/{$id}?date=2099-01-01")->assertStatus(405);
        $this->get("/Comments/{$id}?Description=hi")->assertStatus(405);
        $this->get("/checkout/{$id}")->assertStatus(405);
        $this->get("/wishList/{$id}")->assertStatus(405);
        $this->assertSame(0, Comment::count());
        $this->assertSame(0, Booking::count());
    }

    public function test_guests_cannot_post_state_changes(): void
    {
        $id = $this->property->id;

        $this->post("/Comments/{$id}", ['Description' => 'hi'])->assertRedirect(route('login'));
        $this->post("/Booking/{$id}", ['date' => '2099-01-01'])->assertRedirect(route('login'));
        $this->post("/wishList/{$id}")->assertRedirect(route('login'));
        $this->post("/checkout/{$id}", ['card_last4' => '4242'])->assertRedirect(route('login'));
    }

    public function test_wishlist_toggle_and_comment_work_with_post(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $this->post("/wishList/{$this->property->id}")->assertRedirect();
        $this->assertTrue($user->Favourite_Property()->whereKey($this->property->id)->exists());
        $this->post("/wishList/{$this->property->id}")->assertRedirect();
        $this->assertFalse($user->Favourite_Property()->whereKey($this->property->id)->exists());

        $this->post("/Comments/{$this->property->id}", ['Description' => 'Lovely'])->assertRedirect();
        $this->assertSame(1, Comment::where('user_id', $user->id)->count());
    }


    public function test_card_secrets_have_no_column_to_live_in(): void
    {
        foreach (['card_number', 'cvv', 'expDate'] as $column) {
            $this->assertFalse(Schema::hasColumn('checkouts', $column), "checkouts.$column must not exist");
        }
        $this->assertTrue(Schema::hasColumn('checkouts', 'card_last4'));
    }

    public function test_checkout_stores_only_the_last_four_digits(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $this->post("/checkout/{$this->property->id}", [
            'card_last4' => '4242',
            'CardNumber' => '4111111111114242',
            'cvv' => '123',
            'expDate' => '2099-01-01',
            'address' => '12 Buyer St',
        ])->assertRedirect()->assertSessionHas('CheckoutSuccess');

        $row = \DB::table('checkouts')->first();
        $this->assertSame('4242', $row->card_last4);
        $this->assertEquals($user->id, $row->user_id);
        $this->assertEquals($this->dealer->id, $row->dealer_id);
        $this->assertStringNotContainsString('4111', json_encode($row));
        $this->assertSame('Not Available', $this->property->fresh()->status);
    }

    public function test_checkout_rejects_a_missing_or_malformed_card_suffix(): void
    {
        $this->actingAs($this->makeUser());

        foreach ([[], ['card_last4' => '12'], ['card_last4' => '4111111111114242'], ['card_last4' => 'abcd']] as $body) {
            $this->post("/checkout/{$this->property->id}", $body)->assertSessionHasErrors('card_last4');
        }
        $this->assertSame(0, \DB::table('checkouts')->count());
    }

    public function test_an_unavailable_property_cannot_be_bought(): void
    {
        $this->property->update(['status' => 'Not Available']);
        $this->actingAs($this->makeUser());

        $this->post("/checkout/{$this->property->id}", ['card_last4' => '4242'])->assertSessionHas('error');
        $this->assertSame(0, \DB::table('checkouts')->count());
    }
}
