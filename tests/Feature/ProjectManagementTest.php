<?php

declare(strict_types=1);

use Domain\Project\Enums\ProjectStatus;
use Domain\Tenant\Entities\Tenant;

it('creates a project for the requested tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenant = Tenant::factory()->create();

    $response = $this->postJson("/api/tenants/{$tenant->id}/projects", [
        'name' => 'Q2 Platform Launch',
        'description' => 'Delivery plan and milestones.',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.tenant_id', $tenant->id)
        ->assertJsonPath('data.name', 'Q2 Platform Launch')
        ->assertJsonPath('data.status', ProjectStatus::Active->value);

    $this->assertDatabaseHas('projects', [
        'tenant_id' => $tenant->id,
        'name' => 'Q2 Platform Launch',
        'status' => ProjectStatus::Active->value,
    ]);
});
