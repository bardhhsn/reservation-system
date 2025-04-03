<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;

Route::apiResource('tables', TableController::class);
Route::apiResource('reservations', ReservationController::class);
