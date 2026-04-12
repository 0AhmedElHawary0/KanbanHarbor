<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Presentation\UserManagement\Controllers\AuthController;

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::get('auth/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('auth/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
    ->middleware(['throttle:6,1'])
    ->name('verification.send');

Route::middleware(['auth:sanctum'])->controller(AuthController::class)->group(function (): void {
    Route::post('auth/logout', 'logout');
});
