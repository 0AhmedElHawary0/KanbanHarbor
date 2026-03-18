<?php

declare(strict_types=1);

use Domain\Tenant\Entities\Tenant;
use Domain\User\Entities\User;
use Domain\User\Enums\UserRole;
use Domain\User\Enums\UserStatus;

it('creates a tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $response = $this->postJson('/api/tenants', [
        'name' => 'Harbor Operations',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Harbor Operations');

    $this->assertDatabaseHas('tenants', [
        'name' => 'Harbor Operations',
    ]);
});

it('adds a tenant member', function (): void {
    /** @var \Tests\TestCase $this */

    $tenant = Tenant::factory()->create();

    $response = $this->postJson("/api/tenants/{$tenant->id}/members", [
        'name' => 'Mina Ashraf',
        'email' => 'mina@example.com',
        'password' => 'secret123',
        'status' => UserStatus::Active->value,
        'role' => UserRole::Admin->value,
    ]);

    $response->assertCreated();

    $user = User::where('email', 'mina@example.com')->first();
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'mina@example.com',
    ]);

    $this->assertDatabaseHas('tenant_user', [
        'user_id' => $user->id,
        'tenant_id' => $tenant->id,
        'role' => UserRole::Admin->value,
    ]);
});

it('lists tenant members for the requested tenant only', function (): void {
    /** @var \Tests\TestCase $this */

    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $usersA = User::factory()->count(2)->create();
    $usersB = User::factory()->count(1)->create();

    foreach ($usersA as $user) {
        $user->tenants()->attach($tenantA->id, ['role' => UserRole::Member->value]);
    }

    foreach ($usersB as $user) {
        $user->tenants()->attach($tenantB->id, ['role' => UserRole::Owner->value]);
    }

    $response = $this->getJson("/api/tenants/{$tenantA->id}/members");

    $response->assertOk()
        ->assertJsonCount(2);
});

it('updates a tenant member role within the same tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenant = Tenant::factory()->create();
    $member = User::factory()->create();
    $member->tenants()->attach($tenant->id, ['role' => UserRole::Member->value]);

    $response = $this->patchJson("/api/tenants/{$tenant->id}/members/{$member->id}/role", [
        'role' => UserRole::Admin->value,
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('tenant_user', [
        'user_id' => $member->id,
        'tenant_id' => $tenant->id,
        'role' => UserRole::Admin->value,
    ]);
});

it('does not update a member outside the current tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();
    $member = User::factory()->create();
    $member->tenants()->attach($tenantB->id, ['role' => UserRole::Member->value]);

    $response = $this->patchJson("/api/tenants/{$tenantA->id}/members/{$member->id}/role", [
        'role' => UserRole::Admin->value,
    ]);

    $response->assertNotFound();
});
