<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationChangeRequestController;
use App\Http\Controllers\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/change-requests', [ReservationChangeRequestController::class, 'store']);
});
Route::apiResource('reservations', ReservationController::class);
Route::apiResource('tables', TableController::class);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/change-requests', [ReservationChangeRequestController::class, 'index']);
    Route::post('/change-requests', [ReservationChangeRequestController::class, 'store']);
    Route::post('/change-requests/{id}/approve', [ReservationChangeRequestController::class, 'approve']);
    Route::post('/change-requests/{id}/reject', [ReservationChangeRequestController::class, 'reject']);
});

