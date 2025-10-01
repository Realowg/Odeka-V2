<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PostController;

/*
|--------------------------------------------------------------------------
| Post API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('post', [PostController::class, 'index']);
    Route::post('post', [PostController::class, 'store']);
    Route::get('post/{id}', [PostController::class, 'show']);
    Route::put('post/{id}', [PostController::class, 'update']);
    Route::delete('post/{id}', [PostController::class, 'destroy']);

    // Custom: like
    Route::post('post/like', [PostController::class, 'like']);
    // Custom: unlike
    Route::post('post/unlike', [PostController::class, 'unlike']);
    // Custom: bookmark
    Route::post('post/bookmark', [PostController::class, 'bookmark']);
    // Custom: report
    Route::post('post/report', [PostController::class, 'report']);
});
