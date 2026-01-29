<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\YogaClassController; 
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CreditController;
// use App\Http\Controllers\Api\PaymentController;


// Rutas Públicas
Route::get('/classes', [YogaClassController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas Protegidas (Requieren Token)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // --- ZONA DE RESERVAS ---
    Route::post('/bookings', [BookingController::class, 'store']);  
    Route::get('/bookings', [BookingController::class, 'index']);     
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']); 
    
    // --- ZONA DE CRÉDITOS ---
    Route::post('/buy-credits', [CreditController::class, 'buy']);
    

    // --- ZONA DE ADMIN ---
    Route::middleware('admin')->group(function () { 
        Route::post('/classes', [YogaClassController::class, 'store']);
        Route::delete('/classes/{id}', [YogaClassController::class, 'destroy']); 
    });

});