<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\YogaClassController; 
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CreditController;


Route::get('/classes', [YogaClassController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/my-bookings', [BookingController::class, 'index']); 
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
    
    Route::post('/buy-credits', [PaymentController::class, 'simulateBuyingCredits']);

    Route::post('/buy-credits', [CreditController::class, 'buy']);
    

    Route::middleware('admin')->group(function () {
        Route::post('/classes', [YogaClassController::class, 'store']);
    });

});