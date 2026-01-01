<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KavlingController;
use App\Http\Controllers\Api\PeralatanController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// API Version 1
Route::prefix('v1')->group(function () {

    // ========================
    // Authentication
    // ========================
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/user', [AuthController::class, 'updateProfile']);
        Route::post('/user/avatar', [AuthController::class, 'updateAvatar']);
        Route::post('/user/change-password', [AuthController::class, 'changePassword']);
    });

    // ========================
    // Public Routes (Read Only)
    // ========================
    Route::get('/kavlings', [KavlingController::class, 'index']);
    Route::get('/kavlings/{kavling}', [KavlingController::class, 'show']);

    Route::get('/peralatan', [PeralatanController::class, 'index']);
    Route::get('/peralatan/{peralatan}', [PeralatanController::class, 'show']);

    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::get('/weather', [WeatherController::class, 'current']);

    // Debug Endpoint
    Route::get('/health', function () {
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            return response()->json([
                'status' => 'ok',
                'database' => 'connected',
                'version' => 'debug-v2-' . now()->timestamp,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'database' => $e->getMessage(),
                'version' => 'debug-v2-' . now()->timestamp,
            ], 500);
        }
    });

    Route::get('/galleries', [GalleryController::class, 'index']);

    // ========================
    // Protected Routes (Auth Required)
    // ========================
    Route::middleware('auth:sanctum')->group(function () {
        // Bookings
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::get('/bookings/{booking}', [BookingController::class, 'show']);
        Route::post('/bookings/{booking}/upload-payment', [BookingController::class, 'uploadPayment']);
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);

        // Gallery upload
        Route::post('/galleries', [GalleryController::class, 'store']);
    });
});
