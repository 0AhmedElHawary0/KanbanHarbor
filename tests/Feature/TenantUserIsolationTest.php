<?php

declare(strict_types=1);

use Domain\Tenant\Entities\Tenant;
use Domain\User\Entities\User;
use Domain\User\Enums\UserStatus;

it('lists users only for the current tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    User::factory()->count(2)->create(['tenant_id' => $tenantA->id]);
    User::factory()->count(3)->create(['tenant_id' => $tenantB->id]);

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenantA->id)
        ->get('/api/users');

    $response->assertOk();
    $response->assertJsonCount(2);

    collect($response->json())->each(function (array $user) use ($tenantA): void {
        expect($user['tenant_id'])->toBe($tenantA->id);
    });
});

it('does not show a user from another tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $foreignUser = User::factory()->create(['tenant_id' => $tenantB->id]);

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenantA->id)
        ->get("/api/users/{$foreignUser->id}");

    $response->assertNotFound();
});

it('does not update a user from another tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $foreignUser = User::factory()->create([
        'tenant_id' => $tenantB->id,
        'status' => UserStatus::Active,
    ]);

    $payload = [
        'name' => 'Cross Tenant Update',
        'email' => 'cross-tenant@example.com',
        'password' => 'secret123',
        'status' => UserStatus::Suspended->value,
    ];

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenantA->id)
        ->patch("/api/users/{$foreignUser->id}", $payload);

    $response->assertNotFound();

    $this->assertDatabaseHas('users', [
        'id' => $foreignUser->id,
        'tenant_id' => $tenantB->id,
        'email' => $foreignUser->email,
        'status' => UserStatus::Active->value,
    ]);
});

it('rejects requests without tenant context', function (): void {
    /** @var \Tests\TestCase $this */

    $response = $this->get('/api/users');

    $response->assertBadRequest();
});
