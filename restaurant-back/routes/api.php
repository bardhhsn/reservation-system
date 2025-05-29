<?php
// routes/api.php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Reservation System API",
 *     description="Dokumentacioni i API-ve për sistemin e rezervimeve",
 *     @OA\Contact(name="Support", email="support@example.com")
 * )
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationChangeRequestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VerificationController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);    // no named route here
Route::get('/tables',    [TableController::class, 'index']);

// Email verification (publicly accessible signed URL)
Route::get(
    '/email/verify/{id}/{hash}',
    [VerificationController::class, 'verify']
)
->middleware('signed')
->name('verification.verify');

// All other routes require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Authentication management
    Route::post('/logout',          [AuthController::class, 'logout']);
    Route::post('/refresh',         [AuthController::class, 'refresh']);
    Route::get('/user',             [AuthController::class, 'user']);
    Route::post('/password/change', [AuthController::class, 'changePassword']);

    // Resend verification email
    Route::post(
        '/email/resend',
        [VerificationController::class, 'resend']
    )
    ->name('verification.resend');

    // Admin-only endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/admin/statistics', [AdminController::class, 'statistics']);
        Route::post('/change-requests/{id}/approve', [ReservationChangeRequestController::class, 'approve']);
        Route::post('/change-requests/{id}/reject',  [ReservationChangeRequestController::class, 'reject']);
        Route::get('/change-requests', [ReservationChangeRequestController::class, 'index']); // Admin: get all change requests
    });

    // User-only endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/reservations',      [ReservationController::class, 'index']);
        Route::post('/reservations',     [ReservationController::class, 'store']);
        Route::get('/reservations/{id}', [ReservationController::class, 'show']);

        Route::post('/change-requests',        [ReservationChangeRequestController::class, 'store']);
        Route::get('/change-requests/my',      [ReservationChangeRequestController::class, 'my']);
        Route::delete('/change-requests/{id}', [ReservationChangeRequestController::class, 'destroy']);
    });
});
