<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PostController;

/*
|--------------------------------------------------------------------------
| Posts API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Feed & Posts
    Route::get('posts', [PostController::class, 'index']); // Feed
    Route::post('posts', [PostController::class, 'store']);
    Route::get('posts/{id}', [PostController::class, 'show']);
    Route::put('posts/{id}', [PostController::class, 'update']);
    Route::delete('posts/{id}', [PostController::class, 'destroy']);

    // Likes
    Route::post('posts/{id}/like', [PostController::class, 'like']);
    Route::delete('posts/{id}/like', [PostController::class, 'unlike']);
    Route::get('posts/{id}/likes', [PostController::class, 'likes']);

    // Bookmarks
    Route::post('posts/{id}/bookmark', [PostController::class, 'bookmark']);
    Route::delete('posts/{id}/bookmark', [PostController::class, 'unbookmark']);
    Route::get('posts/bookmarks/list', [PostController::class, 'bookmarks']);

    // Report
    Route::post('posts/{id}/report', [PostController::class, 'report']);
});
