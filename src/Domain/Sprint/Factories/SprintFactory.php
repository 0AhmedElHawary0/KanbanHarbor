<?php

declare(strict_types=1);

namespace Domain\Sprint\Factories;

use Domain\Sprint\Entities\Sprint;
use Domain\Sprint\Enums\SprintStatus;
use Domain\Tenant\Entities\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sprint>
 */
class SprintFactory extends Factory
{
    protected $model = Sprint::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'project_id' => Tenant::factory(),
            'name' => fake()->sentence(3),
            'goal' => fake()->paragraph(),
            'status' => SprintStatus::Active,
            'starts_at' => fake()->date(),
            'ends_at' => fake()->date(),
        ];
    }
}
