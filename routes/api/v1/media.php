<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MediaController;

/*
|--------------------------------------------------------------------------
| Media API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('media', [MediaController::class, 'index']);
    Route::post('media', [MediaController::class, 'store']);
    Route::get('media/{id}', [MediaController::class, 'show']);
    Route::put('media/{id}', [MediaController::class, 'update']);
    Route::delete('media/{id}', [MediaController::class, 'destroy']);

    // Custom: upload
    Route::post('media/upload', [MediaController::class, 'upload']);
    // Custom: download
    Route::post('media/download', [MediaController::class, 'download']);
    // Custom: encode
    Route::post('media/encode', [MediaController::class, 'encode']);
});
