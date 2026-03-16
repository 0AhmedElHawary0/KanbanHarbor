<?php

declare(strict_types=1);

namespace Domain\Project\Factories;

use Domain\Project\Entities\Project;
use Domain\Project\Enums\ProjectStatus;
use Domain\Tenant\Entities\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => ProjectStatus::Active,
        ];
    }
}
