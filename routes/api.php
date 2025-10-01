<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PushNotificationsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Legacy endpoints (keep for backwards compatibility)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('device/register', [PushNotificationsController::class, 'registerDevice']);
Route::get('device/delete', [PushNotificationsController::class, 'deleteDevice']);

/*
|--------------------------------------------------------------------------
| API v1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Load all v1 route files
    require __DIR__ . '/api/v1/auth.php';
    require __DIR__ . '/api/v1/users.php';
    require __DIR__ . '/api/v1/messages.php';
    require __DIR__ . '/api/v1/subscriptions.php';
    require __DIR__ . '/api/v1/posts.php';
    require __DIR__ . '/api/v1/payments.php';
    require __DIR__ . '/api/v1/media.php';
    require __DIR__ . '/api/v1/live.php';
    require __DIR__ . '/api/v1/shop.php';
    require __DIR__ . '/api/v1/notifications.php';
    require __DIR__ . '/api/v1/admin.php';
    require __DIR__ . '/api/v1/odeva.php';
});
