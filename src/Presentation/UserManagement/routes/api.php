<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Presentation\UserManagement\Controllers\AuthController;

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->controller(AuthController::class)->group(function (): void {
    Route::post('auth/logout', 'logout');
});
