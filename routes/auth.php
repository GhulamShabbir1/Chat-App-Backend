<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'forgotPassword'])->middleware('validate.forgot.password');
Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'resetPassword'])->middleware('validate.reset.password');

// Protected auth routes
Route::middleware(['custom.auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
});
