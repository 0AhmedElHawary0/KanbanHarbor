<?php

declare(strict_types=1);

use Domain\User\Entities\User;
use Domain\User\Enums\UserStatus;
use Domain\Tenant\Entities\Tenant;

it('creates users', function (): void {
    /** @var \Tests\TestCase $this */

    User::factory()->count(5)->create();

    $userCount = User::count();

    $this->assertEquals(5, $userCount);
});


it('retrieves all users', function (): void {
    /** @var \Tests\TestCase $this */

    $tenant = Tenant::factory()->create();

    User::factory()->count(5)->create(['tenant_id' => $tenant->id]);

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenant->id)
        ->get('/api/users');

    $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => ['id', 'tenant_id', 'name', 'email', 'status', 'created_at', 'updated_at'],
        ]);

    $this->assertCount(5, $response->json());
});

it('updates a user', function (): void {
    /** @var \Tests\TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com',
        'password' => '123456789',
        'status' => UserStatus::Active->value,
    ]);

    $updateData = [
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com',
        'password' => '123456756',
        'status' => UserStatus::Suspended->value,
    ];

    $response = $this
        ->withHeader('X-Tenant-Id', (string) $tenant->id)
        ->patch("/api/users/{$user->id}", $updateData);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'tenant_id' => $tenant->id,
                'name' => 'Muqtadir Khan',
                'email' => 'muqtadir.khan@gmail.com',
                'status' => UserStatus::Suspended->value,
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'tenant_id' => $tenant->id,
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com',
        'status' => UserStatus::Suspended->value,
    ]);
});
