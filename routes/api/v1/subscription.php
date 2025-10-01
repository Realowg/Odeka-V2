<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Subscription API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('subscription', [SubscriptionController::class, 'index']);
    Route::post('subscription', [SubscriptionController::class, 'store']);
    Route::get('subscription/{id}', [SubscriptionController::class, 'show']);
    Route::put('subscription/{id}', [SubscriptionController::class, 'update']);
    Route::delete('subscription/{id}', [SubscriptionController::class, 'destroy']);

    // Custom: cancel
    Route::post('subscription/cancel', [SubscriptionController::class, 'cancel']);
    // Custom: renew
    Route::post('subscription/renew', [SubscriptionController::class, 'renew']);
});
