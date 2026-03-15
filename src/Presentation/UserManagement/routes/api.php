<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Presentation\Tenancy\Middlewares\ResolveTenant;
use Presentation\UserManagement\Controllers\UserController;

Route::get('/user', fn(Request $request) => $request->user())->middleware('auth:sanctum');

Route::middleware(ResolveTenant::class)->controller(UserController::class)->group(function (): void {
    Route::post('users', 'store');
    Route::patch('users/{id}', 'update');
    Route::get('users/{id}', 'show');
    Route::get('users', 'index');
});
