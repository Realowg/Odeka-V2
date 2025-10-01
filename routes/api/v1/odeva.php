<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\OdevaController;

/*
|--------------------------------------------------------------------------
| Odeva API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('odeva', [OdevaController::class, 'index']);
    Route::post('odeva', [OdevaController::class, 'store']);
    Route::get('odeva/{id}', [OdevaController::class, 'show']);
    Route::put('odeva/{id}', [OdevaController::class, 'update']);
    Route::delete('odeva/{id}', [OdevaController::class, 'destroy']);

    // Custom: chat
    Route::post('odeva/chat', [OdevaController::class, 'chat']);
    // Custom: functions
    Route::post('odeva/functions', [OdevaController::class, 'functions']);
    // Custom: execute
    Route::post('odeva/execute', [OdevaController::class, 'execute']);
    // Custom: context
    Route::post('odeva/context', [OdevaController::class, 'context']);
    // Custom: automation
    Route::post('odeva/automation', [OdevaController::class, 'automation']);
    // Custom: subscribe
    Route::post('odeva/subscribe', [OdevaController::class, 'subscribe']);
});
