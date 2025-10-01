<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MessageController;

/*
|--------------------------------------------------------------------------
| Messages API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Conversations
    Route::get('messages/conversations', [MessageController::class, 'index']);
    Route::get('messages/conversations/{userId}', [MessageController::class, 'show']);
    
    // Send message
    Route::post('messages/send', [MessageController::class, 'send']);
    
    // Message actions
    Route::delete('messages/{id}', [MessageController::class, 'destroy']);
    Route::post('messages/{id}/read', [MessageController::class, 'markRead']);
    Route::post('messages/users/{userId}/read-all', [MessageController::class, 'markAllRead']);
    
    // Unread counts
    Route::get('messages/unread-count', [MessageController::class, 'unreadCount']);
    Route::get('messages/unread-by-user', [MessageController::class, 'unreadCountByUser']);
});
