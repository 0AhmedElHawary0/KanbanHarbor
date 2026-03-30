<?php

declare(strict_types=1);

use Domain\Tenant\Entities\Tenant;
use Domain\User\Entities\User;
use Domain\User\Enums\UserRole;
use Spatie\Permission\Models\Permission;

function tenantActor(\Tests\TestCase $test, Tenant $tenant, string $permission): User
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

it('creates a tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $creator = User::factory()->create();
    /** @var User $creator */
    $this->actingAs($creator);

    $response = $this->postJson('/api/tenants', [
        'name' => 'Harbor Operations',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Harbor Operations');

    $this->assertDatabaseHas('tenants', [
        'name' => 'Harbor Operations',
    ]);

    $tenantId = (int) $response->json('data.id');

    $this->assertDatabaseHas('tenant_user', [
        'tenant_id' => $tenantId,
        'user_id' => $creator->id,
        'role' => UserRole::Owner->value,
    ]);
});

it('adds a tenant member', function (): void {
    /** @var \Tests\TestCase $this */

    $tenant = Tenant::factory()->create();
    tenantActor($this, $tenant, 'member.invite');
    $invitedUser = User::factory()->create([
        'email' => 'mina@example.com',
    ]);

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenant->id)
        ->postJson("/api/tenants/{$tenant->id}/members", [
            'email' => 'mina@example.com',
            'role' => UserRole::Admin->value,
        ]);

    $response->assertCreated();

    $this->assertDatabaseHas('tenant_user', [
        'user_id' => $invitedUser->id,
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

    tenantActor($this, $tenantA, 'member.view');

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenantA->id)
        ->getJson("/api/tenants/{$tenantA->id}/members");

    $response->assertOk()
        ->assertJsonCount(3);
});

it('updates a tenant member role within the same tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenant = Tenant::factory()->create();
    tenantActor($this, $tenant, 'member.role.update');

    $member = User::factory()->create();
    $member->tenants()->attach($tenant->id, ['role' => UserRole::Member->value]);

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenant->id)
        ->patchJson("/api/tenants/{$tenant->id}/members/{$member->id}/role", [
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
    tenantActor($this, $tenantA, 'member.role.update');

    $member = User::factory()->create();
    $member->tenants()->attach($tenantB->id, ['role' => UserRole::Member->value]);

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenantA->id)
        ->patchJson("/api/tenants/{$tenantA->id}/members/{$member->id}/role", [
            'role' => UserRole::Admin->value,
        ]);

    $response->assertNotFound();
});
