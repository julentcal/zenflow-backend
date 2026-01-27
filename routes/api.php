<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\YogaClassController; 
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AuthController;


Route::get('/classes', [YogaClassController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/my-bookings', [BookingController::class, 'index']); 
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
});