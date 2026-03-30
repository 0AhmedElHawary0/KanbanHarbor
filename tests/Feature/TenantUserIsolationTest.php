<?php

declare(strict_types=1);

use Domain\Tenant\Entities\Tenant;
use Domain\User\Entities\User;
use Domain\User\Enums\UserRole;
use Domain\User\Enums\UserStatus;
use Spatie\Permission\Models\Permission;

function actingUserWithTenantPermission(\Tests\TestCase $test, Tenant $tenant, string $permission): User
{
    $actor = User::factory()->create();
    /** @var User $actor */
    $actor->tenants()->attach($tenant->id, ['role' => UserRole::Admin->value]);

    setPermissionsTeamId($tenant->id);

    try {
        Permission::findOrCreate($permission, 'web');
        $actor->givePermissionTo($permission);
    } finally {
        setPermissionsTeamId(null);
    }

    $test->actingAs($actor);

    return $actor;
}

it('lists users only for the current tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $usersA = User::factory()->count(2)->create();
    $usersB = User::factory()->count(3)->create();

    foreach ($usersA as $user) {
        $user->tenants()->attach($tenantA->id, ['role' => UserRole::Member->value]);
    }

    foreach ($usersB as $user) {
        $user->tenants()->attach($tenantB->id, ['role' => UserRole::Member->value]);
    }

    actingUserWithTenantPermission($this, $tenantA, 'member.view');

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenantA->id)
        ->getJson("/api/tenants/{$tenantA->id}/members");

    $response->assertOk();
    $response->assertJsonCount(3);
    $response->assertJsonFragment(['id' => $usersA[0]->id]);
    $response->assertJsonFragment(['id' => $usersA[1]->id]);
    $response->assertJsonMissing(['id' => $usersB[0]->id]);
});

it('does not update a user role from another tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    actingUserWithTenantPermission($this, $tenantA, 'member.role.update');

    $foreignUser = User::factory()->create();
    $foreignUser->tenants()->attach($tenantB->id, ['role' => UserRole::Member->value]);

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenantA->id)
        ->patchJson("/api/tenants/{$tenantA->id}/members/{$foreignUser->id}/role", [
            'role' => UserRole::Admin->value,
        ]);

    $response->assertNotFound();
});

it('does not update a user from another tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    actingUserWithTenantPermission($this, $tenantA, 'member.role.update');

    $foreignUser = User::factory()->create([
        'status' => UserStatus::Active,
    ]);

    $foreignUser->tenants()->attach($tenantB->id, ['role' => UserRole::Member->value]);

    $payload = [
        'role' => UserRole::Admin->value,
    ];

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenantA->id)
        ->patchJson("/api/tenants/{$tenantA->id}/members/{$foreignUser->id}/role", $payload);

    $response->assertNotFound();

    $this->assertDatabaseHas('users', [
        'id' => $foreignUser->id,
        'email' => $foreignUser->email,
        'status' => UserStatus::Active->value,
    ]);

    $this->assertDatabaseHas('tenant_user', [
        'user_id' => $foreignUser->id,
        'tenant_id' => $tenantB->id,
        'role' => UserRole::Member->value,
    ]);
});

it('rejects requests without tenant context', function (): void {
    /** @var \Tests\TestCase $this */

    $user = User::factory()->create();
    /** @var User $user */
    $this->actingAs($user);

    $response = $this->getJson('/api/tenants/not-valid/members');

    $response->assertBadRequest();
});
