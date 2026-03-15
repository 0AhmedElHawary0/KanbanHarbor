<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Presentation\TenantManagement\Controllers\TenantController;
use Presentation\Tenancy\Middlewares\ResolveTenant;

Route::post('tenants', [TenantController::class, 'store']);

Route::middleware(ResolveTenant::class)->controller(TenantController::class)->group(function (): void {
    Route::post('tenants/{tenantId}/members', 'storeMember');
    Route::patch('tenants/{tenantId}/members/{userId}/role', 'updateMemberRole');
    Route::get('tenants/{tenantId}/members', 'members');
});
