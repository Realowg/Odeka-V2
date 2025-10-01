<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\StoryController;

/*
|--------------------------------------------------------------------------
| Story API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('story', [StoryController::class, 'index']);
    Route::post('story', [StoryController::class, 'store']);
    Route::get('story/{id}', [StoryController::class, 'show']);
    Route::put('story/{id}', [StoryController::class, 'update']);
    Route::delete('story/{id}', [StoryController::class, 'destroy']);

    // Custom: view
    Route::post('story/view', [StoryController::class, 'view']);
    // Custom: viewers
    Route::post('story/viewers', [StoryController::class, 'viewers']);
});
