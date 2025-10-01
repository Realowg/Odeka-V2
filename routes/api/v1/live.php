<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LiveController;

/*
|--------------------------------------------------------------------------
| Live API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('live', [LiveController::class, 'index']);
    Route::post('live', [LiveController::class, 'store']);
    Route::get('live/{id}', [LiveController::class, 'show']);
    Route::put('live/{id}', [LiveController::class, 'update']);
    Route::delete('live/{id}', [LiveController::class, 'destroy']);

    // Custom: start
    Route::post('live/start', [LiveController::class, 'start']);
    // Custom: stop
    Route::post('live/stop', [LiveController::class, 'stop']);
    // Custom: viewers
    Route::post('live/viewers', [LiveController::class, 'viewers']);
    // Custom: join
    Route::post('live/join', [LiveController::class, 'join']);
});
