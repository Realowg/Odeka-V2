<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;

/*
|--------------------------------------------------------------------------
| Users API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Current user
    Route::get('users/me', [UserController::class, 'me']);
    Route::put('users/me', [UserController::class, 'update']);
    Route::delete('users/me', [UserController::class, 'destroy']);

    // User profile
    Route::get('users/{username}', [UserController::class, 'show']);
    Route::get('users/{id}/posts', [UserController::class, 'posts']);
    Route::get('users/{id}/stats', [UserController::class, 'stats']);

    // Follow/Unfollow
    Route::post('users/{id}/follow', [UserController::class, 'follow']);
    Route::delete('users/{id}/follow', [UserController::class, 'unfollow']);
    Route::get('users/{id}/followers', [UserController::class, 'followers']);
    Route::get('users/{id}/following', [UserController::class, 'following']);

    // Restrict/Unrestrict
    Route::post('users/{id}/restrict', [UserController::class, 'restrict']);
    Route::delete('users/{id}/restrict', [UserController::class, 'unrestrict']);
});
