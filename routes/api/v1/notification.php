<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\NotificationController;

/*
|--------------------------------------------------------------------------
| Notifications API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread', [NotificationController::class, 'unread']);
    Route::put('notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'readAll']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
    Route::post('notifications/preferences', [NotificationController::class, 'preferences']);
    Route::post('notifications/devices', [NotificationController::class, 'registerDevice']);
});
