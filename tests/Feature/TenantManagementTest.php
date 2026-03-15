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

    $response->assertCreated()
        ->assertJsonPath('data.tenant_id', $tenant->id)
        ->assertJsonPath('data.role', UserRole::Admin->value);

    $this->assertDatabaseHas('users', [
        'tenant_id' => $tenant->id,
        'email' => 'mina@example.com',
        'role' => UserRole::Admin->value,
    ]);
});

it('lists tenant members for the requested tenant only', function (): void {
    /** @var \Tests\TestCase $this */

    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    User::factory()->count(2)->create(['tenant_id' => $tenantA->id, 'role' => UserRole::Member]);
    User::factory()->count(1)->create(['tenant_id' => $tenantB->id, 'role' => UserRole::Owner]);

    $response = $this->getJson("/api/tenants/{$tenantA->id}/members");

    $response->assertOk()
        ->assertJsonCount(2);

    collect($response->json())->each(function (array $member) use ($tenantA): void {
        expect($member['tenant_id'])->toBe($tenantA->id);
    });
});

it('updates a tenant member role within the same tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenant = Tenant::factory()->create();
    $member = User::factory()->create([
        'tenant_id' => $tenant->id,
        'role' => UserRole::Member,
    ]);

    $response = $this->patchJson("/api/tenants/{$tenant->id}/members/{$member->id}/role", [
        'role' => UserRole::Admin->value,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.role', UserRole::Admin->value);

    $this->assertDatabaseHas('users', [
        'id' => $member->id,
        'tenant_id' => $tenant->id,
        'role' => UserRole::Admin->value,
    ]);
});

it('does not update a member outside the current tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();
    $member = User::factory()->create([
        'tenant_id' => $tenantB->id,
        'role' => UserRole::Member,
    ]);

    $response = $this->patchJson("/api/tenants/{$tenantA->id}/members/{$member->id}/role", [
        'role' => UserRole::Admin->value,
    ]);

    $response->assertNotFound();
});
