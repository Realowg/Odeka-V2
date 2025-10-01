<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AdminController;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'throttle:api-admin'])->group(function () {
    Route::get('admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('admin/users', [AdminController::class, 'users']);
    Route::put('admin/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('admin/users/{id}', [AdminController::class, 'deleteUser']);
    Route::get('admin/reports', [AdminController::class, 'reports']);
    Route::put('admin/reports/{id}', [AdminController::class, 'handleReport']);
    Route::get('admin/transactions', [AdminController::class, 'transactions']);
    Route::get('admin/settings', [AdminController::class, 'settings']);
    Route::put('admin/settings', [AdminController::class, 'updateSettings']);
    Route::get('admin/analytics', [AdminController::class, 'analytics']);
    Route::get('admin/withdrawals', [AdminController::class, 'withdrawals']);
    Route::put('admin/withdrawals/{id}', [AdminController::class, 'updateWithdrawal']);
});
