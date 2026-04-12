<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Presentation\TenantManagement\Controllers\ProjectController;
use Presentation\TenantManagement\Controllers\SprintController;
use Presentation\TenantManagement\Controllers\TenantController;
use Presentation\Tenancy\Middlewares\ResolveTenant;
use Presentation\TenantManagement\Controllers\TaskController;

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
    Route::patch('tenants/{tenantId}/projects/{projectId}', 'archive')->middleware('permission:project.archive');
});

Route::middleware([ResolveTenant::class, 'auth:sanctum'])->controller(SprintController::class)->group(function (): void {
    Route::post('tenants/{tenantId}/projects/{projectId}/sprints', 'store')->middleware('permission:sprint.create');
    Route::get('tenants/{tenantId}/projects/{projectId}/sprints', 'index')->middleware('permission:sprint.view');
    Route::get('tenants/{tenantId}/projects/{projectId}/sprints/{sprintId}', 'show')->middleware('permission:sprint.view');
    Route::put('tenants/{tenantId}/projects/{projectId}/sprints/{sprintId}', 'update')->middleware('permission:sprint.update');
    Route::patch('tenants/{tenantId}/projects/{projectId}/sprints/{sprintId}', 'archive')->middleware('permission:sprint.archive');
    Route::delete('tenants/{tenantId}/projects/{projectId}/sprints/{sprintId}', 'delete')->middleware('permission:sprint.delete');
    Route::post('tenants/{tenantId}/projects/{projectId}/sprints/{sprintId}/restore', 'restore')->middleware('permission:sprint.restore');
});

Route::middleware([ResolveTenant::class, 'auth:sanctum'])->controller(TaskController::class)->group(function (): void {
    Route::post('tenants/{tenantId}/sprints/{sprintId}/tasks', 'store')->middleware('permission:task.create');
    Route::get('tenants/{tenantId}/sprints/{sprintId}/tasks', 'index')->middleware('permission:task.view');
    Route::get('tenants/{tenantId}/tasks/{taskId}', 'show')->middleware('permission:task.view');
    Route::put('tenants/{tenantId}/tasks/{taskId}', 'update')->middleware('permission:task.update');
    Route::delete('tenants/{tenantId}/tasks/{taskId}', 'delete')->middleware('permission:task.delete');
});
