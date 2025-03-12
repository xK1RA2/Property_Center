<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\propertyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ViewProperty;
use App\Http\Controllers\dealerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\signupController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WhislistController;
use App\Http\Controllers\RatingController;

Route::get("/index ",[HomeController::class,'index'])->name('home');
Route::get("/indexdashboard ",[HomeController::class,'dashboard'])->name('dashboard');

Route::get("/Orders ",[HomeController::class,'Orders'])->name('Orders');

//Property Controllers
Route::controller(propertyController::class)->group(function(){
    Route::get('/property', 'index');
});

Route::get('/Dealers',[dealerController::class,'index'])->name('Dealers');
Route::get('/Dealer/dashboard',[dealerController::class,'dashboard'])->name('dealer.dashboard');


Route::get('/Profile',[UserController::class,'index'])->name('user.profile');


Route::get('/property',[propertyController::class,'create'])->name('property');
Route::post('/property', [propertyController::class ,'store'])->name('property.store');
Route::get('/Details/{property}', [HomeController::class ,'Details'])->name('property.details');
Route::get('/property/{property}', [propertyController::class, 'show'])->name('home.show');

Route::post('/property/{property}/rating',[RatingController::class ,'store'])->name('rate.store');


Route::get("/myProperties ",[propertyController::class,'index']);
Route::post("/Destroy/{property} ",[propertyController::class,'Destroy'])->name('Destroy');
Route::get("/addproperty ",[propertyController::class,'create']);

Route::controller(ViewProperty::class)->group(function(){
    Route::get("/View",'index');
    Route::get("/View/search",'search');
   
});
Route::get('/wishList',[WhislistController::class,'index'])->name('wishList.index');
Route::post('/wishList/{property}',[WhislistController::class,'storeDestroy'])->name('wishList.storeDestroy');

Route::get('/login',[loginController::class,'create'])->name('login');
Route::post('/login',[loginController::class,'store'])->name('login.store');
Route::get('/signup',[signupController::class,'create'])->name('signup');
Route::post('/signup',[signupController::class,'store'])->name('signup.store');


Route::get('/admin',[AdminController::class,'index'])->name('Dashboard');
Route::get('/admin/ManageProperty',[AdminController::class,'ManageProperty'])->name('ManageProperty');
Route::post('/admin/ManageProperty/{property}',[AdminController::class,'DestroyProperty'])->name('DestroyProperty');
Route::get('/admin/ManageUsers',[AdminController::class,'ManageUsers'])->name('ManageUsers');
Route::post('/admin/ManageUsers/{user}',[AdminController::class,'DestroyUsers'])->name('DestroyUsers');
Route::get('/admin/ManageRequests',[AdminController::class,'ManageRequests'])->name('ManageRequests');







