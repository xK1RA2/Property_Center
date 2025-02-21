<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\propertyController;
use App\Http\Controllers\HomeController;


Route::controller(propertyController::class)->group(function(){
    Route::get('/property', 'index');
});

Route::get("/",[HomeController::class,'index'])->name('home');




