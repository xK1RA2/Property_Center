<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\propertyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ViewProperty;
use App\Http\Controllers\signupController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\WhislistController;



Route::controller(propertyController::class)->group(function(){
    Route::get('/property', 'index');
});

Route::get('/property',[propertyController::class,'create'])->name('property');
Route::post('/property', [propertyController::class ,'store'])->name('property.store');

Route::get("/index ",[HomeController::class,'index'])->name('home');

Route::get("/myProperties ",[propertyController::class,'index']);
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









