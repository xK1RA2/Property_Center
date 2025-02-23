<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\propertyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ViewProperty;


Route::controller(propertyController::class)->group(function(){
    Route::get('/property', 'index');
});

Route::get("/index ",[HomeController::class,'index'])->name('home');

Route::get("/myProperties ",[propertyController::class,'index']);

Route::controller(ViewProperty::class)->group(function(){
    Route::get("/View",'index');
    Route::get("/View/search",'search');
    Route::get('/Wishlist','wishlist');
});

Route::get('/login',function(){

    return view('auth/login');

});
Route::get('/signup',function(){

    return view('auth/signup');

});




