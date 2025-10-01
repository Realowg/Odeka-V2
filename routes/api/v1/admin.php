<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AdminController;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('admin', [AdminController::class, 'index']);
    Route::post('admin', [AdminController::class, 'store']);
    Route::get('admin/{id}', [AdminController::class, 'show']);
    Route::put('admin/{id}', [AdminController::class, 'update']);
    Route::delete('admin/{id}', [AdminController::class, 'destroy']);

    // Custom: dashboard
    Route::post('admin/dashboard', [AdminController::class, 'dashboard']);
    // Custom: users
    Route::post('admin/users', [AdminController::class, 'users']);
    // Custom: reports
    Route::post('admin/reports', [AdminController::class, 'reports']);
    // Custom: analytics
    Route::post('admin/analytics', [AdminController::class, 'analytics']);
});
