<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaymentController;

/*
|--------------------------------------------------------------------------
| Payment API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('payment', [PaymentController::class, 'index']);
    Route::post('payment', [PaymentController::class, 'store']);
    Route::get('payment/{id}', [PaymentController::class, 'show']);
    Route::put('payment/{id}', [PaymentController::class, 'update']);
    Route::delete('payment/{id}', [PaymentController::class, 'destroy']);

    // Custom: tip
    Route::post('payment/tip', [PaymentController::class, 'tip']);
    // Custom: ppv
    Route::post('payment/ppv', [PaymentController::class, 'ppv']);
    // Custom: withdraw
    Route::post('payment/withdraw', [PaymentController::class, 'withdraw']);
    // Custom: transactions
    Route::post('payment/transactions', [PaymentController::class, 'transactions']);
});
