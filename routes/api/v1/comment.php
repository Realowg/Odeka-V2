<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CommentController;

/*
|--------------------------------------------------------------------------
| Comment API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('comment', [CommentController::class, 'index']);
    Route::post('comment', [CommentController::class, 'store']);
    Route::get('comment/{id}', [CommentController::class, 'show']);
    Route::put('comment/{id}', [CommentController::class, 'update']);
    Route::delete('comment/{id}', [CommentController::class, 'destroy']);

});
