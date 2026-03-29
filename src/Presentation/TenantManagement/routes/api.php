<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Presentation\TenantManagement\Controllers\ProjectController;
use Presentation\TenantManagement\Controllers\TenantController;
use Presentation\Tenancy\Middlewares\ResolveTenant;

Route::middleware(['auth:sanctum'])->post('tenants', [TenantController::class, 'store']);

Route::middleware([ResolveTenant::class, 'auth:sanctum'])->controller(TenantController::class)->group(function (): void {
    Route::post('tenants/{tenantId}/members', 'storeMember')->middleware('permission:member.invite');
    Route::patch('tenants/{tenantId}/members/{userId}/role', 'updateMemberRole')->middleware('permission:member.role.update');
    Route::get('tenants/{tenantId}/members', 'members')->middleware('permission:member.view');
});

Route::middleware([ResolveTenant::class, 'auth:sanctum'])->controller(ProjectController::class)->group(function (): void {
    Route::post('tenants/{tenantId}/projects', 'store')->middleware('permission:project.create');
    Route::get('tenants/{tenantId}/projects', 'index')->middleware('permission:project.view');
    Route::get('tenants/{tenantId}/projects/{projectId}', 'show')->middleware('permission:project.view');
});
