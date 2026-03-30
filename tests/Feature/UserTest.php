<?php

declare(strict_types=1);

use Domain\User\Entities\User;
use Domain\User\Enums\UserRole;
use Domain\User\Enums\UserStatus;
use Domain\Tenant\Entities\Tenant;
use Spatie\Permission\Models\Permission;

function userTestActor(\Tests\TestCase $test, Tenant $tenant, string $permission): User
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

it('creates users', function (): void {
    /** @var \Tests\TestCase $this */

    User::factory()->count(5)->create();

    $userCount = User::count();

    $this->assertEquals(5, $userCount);
});


it('retrieves all users', function (): void {
    /** @var \Tests\TestCase $this */

    $tenant = Tenant::factory()->create();
    userTestActor($this, $tenant, 'member.view');

    $users = User::factory()->count(5)->create();
    foreach ($users as $user) {
        $user->tenants()->attach($tenant->id, ['role' => UserRole::Member->value]);
    }

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenant->id)
        ->getJson("/api/tenants/{$tenant->id}/members");

    $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => ['id', 'name', 'email', 'status', 'created_at', 'updated_at'],
        ]);

    $this->assertCount(6, $response->json());
});

it('updates a user', function (): void {
    /** @var \Tests\TestCase $this */

    $tenant = Tenant::factory()->create();
    userTestActor($this, $tenant, 'member.role.update');

    $user = User::factory()->create([
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com',
        'status' => UserStatus::Active->value,
    ]);

    $user->tenants()->attach($tenant->id, ['role' => UserRole::Member->value]);

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenant->id)
        ->patchJson("/api/tenants/{$tenant->id}/members/{$user->id}/role", [
            'role' => UserRole::Admin->value,
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => 'Muqtadir Khan',
                'email' => 'muqtadir.khan@gmail.com',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com',
        'status' => UserStatus::Active->value,
    ]);

    $this->assertDatabaseHas('tenant_user', [
        'user_id' => $user->id,
        'tenant_id' => $tenant->id,
        'role' => UserRole::Admin->value,
    ]);
});
