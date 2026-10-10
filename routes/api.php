<?php

use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\LookupController;
use App\Http\Controllers\Api\V1\PropertyController;
use App\Http\Controllers\Api\V1\RatingController;
use App\Http\Controllers\Api\V1\TraderRequestController;
use Illuminate\Support\Facades\Route;

/*
| REST API, served under /api/v1 (JSON only, Sanctum bearer-token auth).
| Send "Accept: application/json" and "Authorization: Bearer <token>".
*/
Route::prefix('v1')->group(function () {

    // ---- Public ----
    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::get('properties', [PropertyController::class, 'index']);
    Route::get('properties/{property}', [PropertyController::class, 'show']);
    Route::get('properties/{property}/comments', [CommentController::class, 'index']);

    Route::get('states', [LookupController::class, 'states']);
    Route::get('cities', [LookupController::class, 'cities']);
    Route::get('property-types', [LookupController::class, 'propertyTypes']);
    Route::get('dealers', [LookupController::class, 'dealers']);

    // ---- Authenticated ----
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('me', [AuthController::class, 'updateProfile']);
        Route::post('me/photo', [AuthController::class, 'updatePhoto']);
        Route::put('me/password', [AuthController::class, 'updatePassword']);

        Route::post('properties/{property}/comments', [CommentController::class, 'store']);
        Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
        Route::put('properties/{property}/rating', [RatingController::class, 'upsert']);

        Route::get('favorites', [FavoriteController::class, 'index']);
        Route::put('properties/{property}/favorite', [FavoriteController::class, 'store']);
        Route::delete('properties/{property}/favorite', [FavoriteController::class, 'destroy']);

        Route::post('devices', [DeviceController::class, 'store']);
        Route::delete('devices', [DeviceController::class, 'destroy']);

        Route::get('bookings', [BookingController::class, 'index']);
        Route::post('properties/{property}/bookings', [BookingController::class, 'store']);

        Route::post('trader-requests', [TraderRequestController::class, 'store'])
            ->middleware('role:' . \App\Models\User::ROLE_USER);

        // ---- Dealer ----
        Route::middleware('role:' . \App\Models\User::ROLE_DEALER)->group(function () {
            Route::get('my/properties', [PropertyController::class, 'mine']);
            Route::post('properties', [PropertyController::class, 'store']);
            Route::patch('bookings/{booking}', [BookingController::class, 'update']);
        });

        // ---- Dealer (own) or admin: ownership enforced by PropertyPolicy ----
        Route::middleware('role:2,3')->group(function () {
            Route::match(['put', 'patch'], 'properties/{property}', [PropertyController::class, 'update']);
            Route::delete('properties/{property}', [PropertyController::class, 'destroy']);
        });

        // ---- Admin ----
        Route::middleware('role:' . \App\Models\User::ROLE_ADMIN)->prefix('admin')->group(function () {
            Route::get('users', [Admin\UserController::class, 'index']);
            Route::delete('users/{user}', [Admin\UserController::class, 'destroy']);
            Route::get('trader-requests', [Admin\TraderRequestController::class, 'index']);
            Route::patch('trader-requests/{traderRequest}', [Admin\TraderRequestController::class, 'update']);
        });
    });
});
