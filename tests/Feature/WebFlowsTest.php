<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Checkout;
use App\Models\Comment;
use App\Models\Location;
use App\Models\Property;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MakesMarketplaceData;
use Tests\TestCase;

/** Behaviour of the Blade site after the controllers were split into single-purpose classes. */
class WebFlowsTest extends TestCase
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
        $this->property->PropertyImages()->create(['image_path' => 'images/x.jpg', 'position' => 1]);
        $this->property->Features()->update(['Bedrooms' => 2, 'Bathrooms' => 1, 'Area' => 90]);
    }

    // ---------- every page still renders ----------

    public function test_public_pages_render(): void
    {
        foreach (['/index', '/Dealers', '/View', '/Search', '/Maps', '/login', '/signup', '/forgot'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get("/Details/{$this->property->id}")->assertOk();
        $this->get('/Dealers/' . $this->dealer->id)->assertOk();
    }

    public function test_signed_in_pages_render(): void
    {
        $this->actingAs($this->makeUser());

        foreach (['/Profile', '/orders', '/wishList'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get("/Details/{$this->property->id}")->assertOk();
    }

    public function test_dealer_pages_render(): void
    {
        Booking::create([
            'user_id' => $this->makeUser()->id, 'dealer_id' => $this->dealer->id, 'property_id' => $this->property->id,
            'date' => now()->addDay(), 'description' => 'visit', 'status' => 'Pending',
        ]);
        $this->actingAs($this->dealer);

        foreach (['/dashboard', '/Dealer/dashboard', '/myProperties', '/property', '/locations'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get("/property/{$this->property->id}/edit")->assertOk();
    }

    public function test_guests_and_regular_users_are_kept_out_of_dealer_pages(): void
    {
        $this->get('/myProperties')->assertRedirect(route('login'));

        $this->actingAs($this->makeUser());
        foreach (['/dashboard', '/myProperties', '/property', "/property/{$this->property->id}/edit"] as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    // ---------- search ----------

    public function test_search_filters_and_sorts(): void
    {
        $this->makeProperty($this->dealer, ['price' => 50, 'PurchaseType' => 'Rent']);
        $this->makeProperty($this->dealer, ['price' => 900, 'PurchaseType' => 'Rent']);

        $this->get('/Search?Purchase_Type=Rent&sort=price')->assertOk()
            ->assertViewHas('Properties', fn ($p) => $p->count() === 2 && $p->first()->price === 50);

        $this->get('/Search?Purchase_Type=Rent&sort=-price')
            ->assertViewHas('Properties', fn ($p) => $p->first()->price === 900);
    }

    public function test_search_rejects_arbitrary_sort_columns(): void
    {
        $this->get('/Search?sort=password')->assertSessionHasErrors('sort');
    }

    // ---------- bookings ----------

    public function test_booking_is_created_then_updated_and_must_be_in_the_future(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $this->post("/Booking/{$this->property->id}", ['date' => now()->addDays(2)->toDateTimeString()])
            ->assertSessionHas('BookingSuccess');
        $this->post("/Booking/{$this->property->id}", ['date' => now()->addDays(3)->toDateTimeString(), 'description' => 'later'])
            ->assertSessionHas('BookingUpdated');

        $this->assertSame(1, Booking::where('user_id', $user->id)->count());
        $this->assertSame('later', Booking::first()->description);
        $this->assertSame('Pending', Booking::first()->status);

        $this->post("/Booking/{$this->property->id}", ['date' => now()->subDay()->toDateTimeString()])
            ->assertSessionHasErrors('date');
    }

    // ---------- ratings: one user must never change another user's rating ----------

    public function test_rating_only_changes_the_callers_own_rating(): void
    {
        $alice = $this->makeUser();
        $bob = $this->makeUser();

        $this->actingAs($alice)->post("/property/{$this->property->id}/rating", ['rating' => 5])->assertSessionHas('RateSubmit');
        $this->actingAs($bob)->post("/property/{$this->property->id}/rating", ['rating' => 1])->assertSessionHas('RateSubmit');

        // Bob changes his mind. Alice's 5 must be untouched (the old code overwrote every rating).
        $this->actingAs($bob)->post("/property/{$this->property->id}/rating", ['rating' => 3])->assertSessionHas('update');

        $this->assertSame(5, Rating::where('user_id', $alice->id)->value('Rate'));
        $this->assertSame(3, Rating::where('user_id', $bob->id)->value('Rate'));
        $this->assertSame(2, Rating::count());
    }

    public function test_rating_value_is_validated(): void
    {
        $this->actingAs($this->makeUser())
            ->post("/property/{$this->property->id}/rating", ['rating' => 9])
            ->assertSessionHasErrors('rating');
    }

    // ---------- comments ----------

    public function test_anonymous_comment_and_owner_only_delete(): void
    {
        $author = $this->makeUser();
        $this->actingAs($author)
            ->post("/Comments/{$this->property->id}", ['Description' => 'Hmm', 'Ananymous' => 'Ananymous'])
            ->assertRedirect();

        $comment = Comment::first();
        $this->assertSame(2, (int) $comment->comment_type_id);

        $this->actingAs($this->makeUser())->delete("/CommentDelete/{$comment->id}")->assertForbidden();
        $this->actingAs($author)->delete("/CommentDelete/{$comment->id}")->assertRedirect();
        $this->assertSame(0, Comment::count());
    }

    public function test_empty_comment_is_rejected(): void
    {
        $this->actingAs($this->makeUser())
            ->post("/Comments/{$this->property->id}", ['Description' => ''])
            ->assertSessionHasErrors('Description');
    }

    // ---------- dealer creates a listing ----------

    public function test_dealer_creates_a_listing_with_address_year_and_photos_then_pins_it(): void
    {
        Storage::fake('public');
        $this->actingAs($this->dealer);

        $this->post('/property', [
            'PropertyPurchase' => 'Rent',
            'PropertyName' => $this->type->id,
            'city_id' => $this->city->id,
            'price' => 650,
            'address' => '7 Real Street',
            'year' => 2015,
            'Description' => 'Nice',
            'area' => 80, 'Bedrooms' => 2, 'Bathrooms' => 1, 'rooms' => 3, 'Kitchen' => 1,
            'Parking' => '1',
            'images' => [$this->fakePng('a.png')],
        ])->assertRedirect(route('locations.create'));

        $created = Property::latest('id')->first();
        $this->assertSame('7 Real Street', $created->address);
        $this->assertSame(2015, $created->year);
        $this->assertSame('Rent', $created->PurchaseType);
        $this->assertNotNull($created->published_at);
        $this->assertEquals(1, $created->Features->Parking);
        $this->assertEquals(0, $created->Features->Heating);
        $this->assertSame(1, $created->PropertyImages()->count());

        // pin goes on THIS listing, even if another dealer created one in the meantime
        $this->makeProperty($this->makeDealer());
        $this->post('/locations', ['lat' => 31.9, 'lng' => 35.9])->assertRedirect();

        $this->assertSame($created->id, Location::first()->property_id);
    }

    public function test_listing_form_requires_valid_references(): void
    {
        $this->actingAs($this->dealer)
            ->post('/property', ['PropertyPurchase' => 'Gift', 'PropertyName' => 999, 'city_id' => 999, 'price' => -1])
            ->assertSessionHasErrors(['PropertyPurchase', 'PropertyName', 'city_id', 'price']);
    }

    public function test_pin_requires_coordinates_and_a_listing_of_your_own(): void
    {
        $dealerWithoutListings = $this->makeDealer();
        $this->actingAs($dealerWithoutListings);

        $this->post('/locations', ['lat' => 'x', 'lng' => 500])->assertSessionHasErrors(['lat', 'lng']);
        $this->post('/locations', ['lat' => 1, 'lng' => 1])->assertNotFound();
        $this->assertSame(0, Location::count());
    }

    // ---------- profile ----------

    public function test_profile_update_checks_uniqueness_against_other_users_only(): void
    {
        $me = $this->makeUser(attrs: ['phone' => '0791111111', 'username' => 'me_name']);
        $other = $this->makeUser(attrs: ['phone' => '0792222222', 'username' => 'taken_name', 'email' => 'taken@example.com']);
        $this->actingAs($me);

        $payload = ['name' => 'Me', 'username' => 'me_name', 'email' => $me->email, 'phone' => '0791111111'];

        $this->post('/InformationUpdate', $payload)->assertSessionHas('Personal'); // keeping my own values is fine
        $this->post('/InformationUpdate', ['email' => $other->email] + $payload)->assertSessionHasErrors('email');
        $this->post('/InformationUpdate', ['phone' => $other->phone] + $payload)->assertSessionHasErrors('phone');
    }

    public function test_password_change_needs_the_current_password_and_min_length(): void
    {
        $user = $this->makeUser(attrs: ['password' => 'current-pass']);
        $this->actingAs($user);

        $this->post('/UpdatePassword', ['password' => 'wrong', 'newPassword' => 'brand-new-pass'])
            ->assertSessionHas('error');
        $this->post('/UpdatePassword', ['password' => 'current-pass', 'newPassword' => 'short'])
            ->assertSessionHasErrors('newPassword');
        $this->post('/UpdatePassword', ['password' => 'current-pass', 'newPassword' => 'brand-new-pass'])
            ->assertSessionHas('PasswordUpdated');

        $this->assertTrue(Hash::check('brand-new-pass', $user->fresh()->password));
    }

    public function test_dealer_request_is_not_duplicated(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $this->post('/Request')->assertRedirect();
        $this->post('/Request')->assertRedirect();

        $this->assertSame(1, \App\Models\TraderRequest::where('user_id', $user->id)->count());
    }

    // ---------- signup / login ----------

    public function test_signup_ignores_a_posted_role_and_enforces_password_rules(): void
    {
        $base = ['name' => 'N', 'username' => 'newbie', 'email' => 'n@example.com', 'phone' => '0793333333'];

        $this->post('/signup', $base + ['password' => 'short', 'password_confirmation' => 'short'])
            ->assertSessionHasErrors('password');

        $this->post('/signup', $base + ['password' => 'long-enough-1', 'password_confirmation' => 'long-enough-1', 'role_id' => 3])
            ->assertRedirect(route('login'));

        $this->assertSame(User::ROLE_USER, User::where('email', 'n@example.com')->value('role_id'));
    }

    public function test_login_and_logout(): void
    {
        $user = $this->makeUser(attrs: ['email' => 'in@example.com', 'password' => 'secret-pass']);

        $this->post('/login', ['email' => 'in@example.com', 'password' => 'nope'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => 'in@example.com', 'password' => 'secret-pass'])->assertRedirect();
        $this->assertAuthenticatedAs($user);

        $this->get('/logout')->assertRedirect();
        $this->assertGuest();
    }

    // ---------- password reset ----------

    public function test_old_unauthenticated_password_overwrite_is_gone(): void
    {
        $victim = $this->makeUser(attrs: ['email' => 'victim@example.com', 'password' => 'original-pass']);

        $this->post('/ForgotUpdate', ['email' => 'victim@example.com', 'password' => 'hacked-pass', 'Confirmpassword' => 'hacked-pass']);

        $this->assertTrue(Hash::check('original-pass', $victim->fresh()->password));
    }

    public function test_reset_link_flow_changes_the_password_and_revokes_api_tokens(): void
    {
        Notification::fake();
        $user = $this->makeUser(attrs: ['email' => 'reset@example.com', 'password' => 'original-pass']);
        $user->createToken('phone');

        $this->post('/ForgotUpdate', ['email' => 'reset@example.com'])->assertSessionHas('status');

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($n) use (&$token) {
            $token = $n->token;

            return true;
        });

        $this->get("/reset-password/{$token}?email=reset@example.com")->assertOk();

        $this->post('/reset-password', [
            'token' => $token, 'email' => 'reset@example.com',
            'password' => 'fresh-password-1', 'password_confirmation' => 'fresh-password-1',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('fresh-password-1', $user->fresh()->password));
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_reset_link_does_not_reveal_whether_an_account_exists(): void
    {
        Notification::fake();

        $this->post('/ForgotUpdate', ['email' => 'nobody@example.com'])
            ->assertSessionHas('status')
            ->assertSessionHasNoErrors();
        Notification::assertNothingSent();
    }

    public function test_reset_with_a_bad_token_fails(): void
    {
        $user = $this->makeUser(attrs: ['password' => 'original-pass']);

        $this->post('/reset-password', [
            'token' => 'not-a-real-token', 'email' => $user->email,
            'password' => 'fresh-password-1', 'password_confirmation' => 'fresh-password-1',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('original-pass', $user->fresh()->password));
    }

    // ---------- checkout through the service ----------

    public function test_two_buyers_cannot_both_buy_the_same_property(): void
    {
        $first = $this->makeUser();
        $second = $this->makeUser();

        $this->actingAs($first)->post("/checkout/{$this->property->id}", ['card_last4' => '1111'])
            ->assertSessionHas('CheckoutSuccess');
        $this->actingAs($second)->post("/checkout/{$this->property->id}", ['card_last4' => '2222'])
            ->assertSessionHas('error');

        $this->assertSame(1, Checkout::count());
        $this->assertEquals($first->id, Checkout::first()->user_id);
    }

    // ---------- misc ----------

    public function test_wishlist_page_lists_saved_properties(): void
    {
        $user = $this->makeUser();
        $user->Favourite_Property()->attach($this->property->id);

        $this->actingAs($user)->get('/wishList')->assertOk()
            ->assertViewHas('Properties', fn ($p) => $p->count() === 1);
    }

    public function test_chatbot_endpoint_answers(): void
    {
        $this->postJson('/botman', [
            'driver' => 'web', 'userId' => '1', 'message' => 'hello', 'attachment' => null,
        ])->assertOk();
    }
}
