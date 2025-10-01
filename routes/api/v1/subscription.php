<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Subscriptions API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // User's subscriptions
    Route::get('subscriptions', [SubscriptionController::class, 'index']);
    Route::get('subscriptions/{id}', [SubscriptionController::class, 'show']);
    Route::post('subscriptions', [SubscriptionController::class, 'store']);
    Route::delete('subscriptions/{id}', [SubscriptionController::class, 'destroy']);
    Route::post('subscriptions/{id}/renew', [SubscriptionController::class, 'renew']);

    // Creator plans
    Route::get('creators/{creatorId}/plans', [SubscriptionController::class, 'creatorPlans']);

    // Subscribers (for creators)
    Route::get('subscribers', [SubscriptionController::class, 'subscribers']);
    Route::get('subscribers/stats', [SubscriptionController::class, 'subscriberStats']);
});
