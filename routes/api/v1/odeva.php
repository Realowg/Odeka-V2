<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\OdevaController;

/*
|--------------------------------------------------------------------------
| Odeva AI API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Chat & Functions
    Route::post('odeva/chat', [OdevaController::class, 'chat']);
    Route::get('odeva/functions', [OdevaController::class, 'functions']);
    Route::post('odeva/functions/execute', [OdevaController::class, 'executeFunction']);
    
    // Context & Automation
    Route::get('odeva/context', [OdevaController::class, 'getContext']);
    Route::get('odeva/automation', [OdevaController::class, 'getAutomation']);
    Route::put('odeva/automation', [OdevaController::class, 'updateAutomation']);
    
    // Subscription Management
    Route::get('odeva/subscription', [OdevaController::class, 'subscription']);
    Route::post('odeva/subscribe', [OdevaController::class, 'subscribe']);
    Route::delete('odeva/subscription', [OdevaController::class, 'cancelSubscription']);
    
    // Analytics
    Route::get('odeva/analytics', [OdevaController::class, 'analytics']);
});
