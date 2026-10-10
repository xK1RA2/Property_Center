<?php

use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\PasswordResetController;
use App\Http\Controllers\Web\Auth\SignupController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\ChatbotController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\CommentController;
use App\Http\Controllers\Web\DealerController;
use App\Http\Controllers\Web\DealerDashboardController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MapController;
use App\Http\Controllers\Web\NewsletterController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\PropertyController;
use App\Http\Controllers\Web\PropertySearchController;
use App\Http\Controllers\Web\RatingController;
use App\Http\Controllers\Web\SocialiteController;
use App\Http\Controllers\Web\WishlistController;
use Illuminate\Support\Facades\Route;

/*
| Blade website. Route NAMES are used all over the views, keep them stable.
| Roles: 1 user, 2 dealer, 3 admin (see App\Models\User::ROLE_*).
| Anything that changes data is POST / DELETE, never GET.
*/

// ---------- public ----------
Route::get('/index', [HomeController::class, 'index'])->name('home');
Route::get('/Dealers', [DealerController::class, 'index'])->name('Dealers');
Route::get('/Dealers/{dealer}', [ProfileController::class, 'dealer'])->name('Dealer_Profile');
Route::get('/Details/{property}', [PropertyController::class, 'details'])->name('property.details');
Route::get('/property/{property}', [PropertyController::class, 'show'])->whereNumber('property')->name('home.show');
Route::get('/View', [PropertySearchController::class, 'index'])->name('Purchase');
Route::get('/Search', [PropertySearchController::class, 'search'])->name('search');
Route::get('/Maps', [MapController::class, 'index'])->name('maps');

// newsletter: double opt-in, the links in the emails are signed
Route::post('/newsletter', [NewsletterController::class, 'subscribe'])->middleware('throttle:5,1')->name('newsletter.subscribe');
Route::get('/newsletter/confirm/{subscriber}', [NewsletterController::class, 'confirm'])->middleware('signed')->name('newsletter.confirm');
Route::get('/newsletter/unsubscribe/{subscriber}', [NewsletterController::class, 'unsubscribe'])->middleware('signed')->name('newsletter.unsubscribe');

// ---------- auth ----------
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
Route::get('/logout', [LoginController::class, 'destroy'])->name('logout');
Route::get('/signup', [SignupController::class, 'create'])->name('signup');
Route::post('/signup', [SignupController::class, 'store'])->middleware('throttle:10,1')->name('signup.store');

Route::get('/forgot', [PasswordResetController::class, 'forgot'])->name('forgot');
Route::post('/ForgotUpdate', [PasswordResetController::class, 'sendLink'])->middleware('throttle:5,1')->name('ForgotUpdate');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

Route::get('/login/oauth/{provider}', [SocialiteController::class, 'redirectToProvider'])->name('login.oauth');
Route::get('/callback/oauth/{provider}', [SocialiteController::class, 'handleCallback']);

// ---------- any signed-in user ----------
Route::middleware('auth')->group(function () {
    Route::get('/Profile', [ProfileController::class, 'show'])->name('user.profile');
    Route::post('/InformationUpdate', [ProfileController::class, 'updateInformation'])->name('InformationUpdate');
    Route::post('/ImageEdit', [ProfileController::class, 'updatePhoto'])->name('EditImage');
    Route::post('/UpdatePassword', [ProfileController::class, 'updatePassword'])->name('UpdatePassword');
    Route::post('/Request', [ProfileController::class, 'requestDealer'])->name('Request');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::post('/checkout/{property}', [CheckoutController::class, 'store'])->name('checkout');

    Route::post('/Booking/{property}', [BookingController::class, 'store'])->name('Booking');

    Route::post('/Comments/{property}', [CommentController::class, 'store'])->name('Comments');
    Route::delete('/CommentDelete/{comment}', [CommentController::class, 'destroy'])->name('CommentDelete');

    Route::post('/property/{property}/rating', [RatingController::class, 'store'])->name('rate.store');

    Route::get('/wishList', [WishlistController::class, 'index'])->name('wishList.index');
    Route::post('/wishList/{property}', [WishlistController::class, 'toggle'])->name('wishList.storeDestroy');
});

// ---------- dealer ----------
Route::middleware(['auth', 'role:2'])->group(function () {
    Route::get('/dashboard', DealerDashboardController::class)->name('index.dashboard');
    Route::get('/Dealer/dashboard', DealerDashboardController::class)->name('dealer.dashboard');

    Route::get('/myProperties', [PropertyController::class, 'index'])->name('myProperties');
    Route::get('/property', [PropertyController::class, 'create'])->name('property');
    Route::post('/property', [PropertyController::class, 'store'])->name('property.store');
    Route::get('/property/{property}/edit', [PropertyController::class, 'edit'])->name('property.edit');

    Route::post('/BookAccept/{booking}', [BookingController::class, 'accept'])->name('BookAccept');
    Route::post('/BookRefuse/{booking}', [BookingController::class, 'refuse'])->name('BookRefuse');

    Route::get('/locations', fn () => view('property.location'))->name('locations.create');
    Route::post('/locations', [MapController::class, 'store'])->name('locations.store');
});

// ---------- dealer (own listing) or admin; ownership is enforced by PropertyPolicy ----------
Route::middleware(['auth', 'role:2,3'])->group(function () {
    Route::post('/property/update/{property}', [PropertyController::class, 'update'])->name('property.update');
    Route::post('/Destroy/{property}', [PropertyController::class, 'destroy'])->name('Destroy');
});

// ---------- admin ----------
Route::middleware(['auth', 'role:3'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('Dashboard');
    Route::get('/ManageProperty', [AdminController::class, 'ManageProperty'])->name('ManageProperty');
    Route::delete('/admin/ManageProperty/{property}', [AdminController::class, 'DestroyProperty'])->name('DestroyProperty');
    Route::get('/admin/EditProperty/{property}', [AdminController::class, 'EditProperty'])->name('EditProperty');
    Route::get('/admin/UpdateProperty/{property}', [AdminController::class, 'UpdateProperty'])->name('UpdateProperty');
    Route::get('/admin/ManageUsers', [AdminController::class, 'ManageUsers'])->name('ManageUsers');
    Route::delete('/admin/ManageUsers/{user}', [AdminController::class, 'DestroyUsers'])->name('DestroyUsers');
    Route::get('/admin/ManageRequests', [AdminController::class, 'ManageRequests'])->name('ManageRequests');
    Route::post('/admin/RequestAction', [AdminController::class, 'Request_Action'])->name('Request_Action');
});

// ---------- chatbot ----------
Route::match(['get', 'post'], '/botman', ChatbotController::class);
