<?php

declare(strict_types=1);

namespace Infrastructure\Project\Persistence\Repositories;

use Application\Project\Data\CreateProjectData;
use Domain\Project\Entities\Project;
use Domain\Project\Enums\ProjectStatus;
use Domain\Project\Repositories\ProjectRepositoryContract;

final class ProjectRepository implements ProjectRepositoryContract
{
    public function create(int $tenantId, CreateProjectData $data): Project
    {
        return Project::query()->create([
            'tenant_id' => $tenantId,
            'name' => $data->name,
            'description' => $data->description,
            'status' => ProjectStatus::Active,
        ]);
    }

    public function findById(int $tenantId, int $projectId): ?Project
    {
        return Project::query()
            ->where('tenant_id', $tenantId)
            ->whereKey($projectId)
            ->first();
    }
}
