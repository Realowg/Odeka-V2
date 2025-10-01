<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ShopController;

/*
|--------------------------------------------------------------------------
| Shop API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('shop', [ShopController::class, 'index']);
    Route::post('shop', [ShopController::class, 'store']);
    Route::get('shop/{id}', [ShopController::class, 'show']);
    Route::put('shop/{id}', [ShopController::class, 'update']);
    Route::delete('shop/{id}', [ShopController::class, 'destroy']);

    // Custom: products
    Route::post('shop/products', [ShopController::class, 'products']);
    // Custom: orders
    Route::post('shop/orders', [ShopController::class, 'orders']);
});
