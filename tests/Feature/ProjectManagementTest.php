<?php

declare(strict_types=1);

use Domain\Project\Entities\Project;
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

it('lists projects only for the requested tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    Project::factory()->count(2)->create(['tenant_id' => $tenantA->id]);
    Project::factory()->count(1)->create(['tenant_id' => $tenantB->id]);

    $response = $this->getJson("/api/tenants/{$tenantA->id}/projects");

    $response->assertOk()
        ->assertJsonCount(2, 'data');

    collect($response->json('data'))->each(function (array $project) use ($tenantA): void {
        expect($project['tenant_id'])->toBe($tenantA->id);
    });
});

it('get only the project requested for the requested tenant', function (): void {
    /** @var \Tests\TestCase $this */

    $tenant = Tenant::factory()->create();

    $project = Project::factory()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Q2 Platform Launch',
    ]);

    $response = $this->getJson("/api/tenants/{$tenant->id}/projects/{$project->id}");
    $response->assertOk()
        ->assertJsonPath('data.id', $project->id)
        ->assertJsonPath('data.tenant_id', $tenant->id)
        ->assertJsonPath('data.name', 'Q2 Platform Launch');
});
