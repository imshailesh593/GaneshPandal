<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest:member')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/auth/verify-token', [AuthController::class, 'verifyToken'])->name('auth.verify-token');
    Route::get('/auth/complete-profile', [AuthController::class, 'showCompleteProfile'])->name('auth.complete-profile');
    Route::post('/auth/complete-profile', [AuthController::class, 'completeProfile'])->name('auth.complete-profile.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth:member')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
