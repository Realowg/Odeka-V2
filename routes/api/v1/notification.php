<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\NotificationController;

/*
|--------------------------------------------------------------------------
| Notification API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('notification', [NotificationController::class, 'index']);
    Route::post('notification', [NotificationController::class, 'store']);
    Route::get('notification/{id}', [NotificationController::class, 'show']);
    Route::put('notification/{id}', [NotificationController::class, 'update']);
    Route::delete('notification/{id}', [NotificationController::class, 'destroy']);

    // Custom: markRead
    Route::post('notification/markRead', [NotificationController::class, 'markRead']);
    // Custom: readAll
    Route::post('notification/readAll', [NotificationController::class, 'readAll']);
    // Custom: preferences
    Route::post('notification/preferences', [NotificationController::class, 'preferences']);
});
