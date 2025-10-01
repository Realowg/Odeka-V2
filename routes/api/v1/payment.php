<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaymentController;

/*
|--------------------------------------------------------------------------
| Payments API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Wallet
    Route::get('payments/wallet', [PaymentController::class, 'wallet']);
    Route::post('payments/add-funds', [PaymentController::class, 'addFunds']);
    
    // Transactions
    Route::get('payments/transactions', [PaymentController::class, 'transactions']);
    Route::get('payments/earnings', [PaymentController::class, 'earnings']);
    
    // Actions
    Route::post('payments/tip', [PaymentController::class, 'tip']);
    Route::post('payments/ppv', [PaymentController::class, 'ppv']);
    
    // Withdrawals
    Route::post('payments/withdraw', [PaymentController::class, 'withdraw']);
    Route::get('payments/withdrawals', [PaymentController::class, 'withdrawals']);
    Route::get('payments/deposits', [PaymentController::class, 'deposits']);
    
    // Methods
    Route::get('payments/methods', [PaymentController::class, 'methods']);
});
