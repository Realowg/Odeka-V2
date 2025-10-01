<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MessageController;

/*
|--------------------------------------------------------------------------
| Message API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('message', [MessageController::class, 'index']);
    Route::post('message', [MessageController::class, 'store']);
    Route::get('message/{id}', [MessageController::class, 'show']);
    Route::put('message/{id}', [MessageController::class, 'update']);
    Route::delete('message/{id}', [MessageController::class, 'destroy']);

    // Custom: send
    Route::post('message/send', [MessageController::class, 'send']);
    // Custom: markRead
    Route::post('message/markRead', [MessageController::class, 'markRead']);
    // Custom: unreadCount
    Route::post('message/unreadCount', [MessageController::class, 'unreadCount']);
});
