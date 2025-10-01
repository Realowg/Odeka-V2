<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CommentController;

/*
|--------------------------------------------------------------------------
| Comments API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('posts/{postId}/comments', [CommentController::class, 'index']);
    Route::post('posts/{postId}/comments', [CommentController::class, 'store']);
    Route::put('comments/{id}', [CommentController::class, 'update']);
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);
});
