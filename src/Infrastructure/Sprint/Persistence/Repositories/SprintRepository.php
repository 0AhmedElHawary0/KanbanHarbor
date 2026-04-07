<?php

declare(strict_types=1);

namespace Infrastructure\Sprint\Persistence\Repositories;

use Application\Sprint\Data\StoreSprintData;
use Domain\Sprint\Entities\Sprint;
use Domain\Sprint\Enums\SprintStatus;
use Domain\Sprint\Repositories\SprintRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

final class SprintRepository implements SprintRepositoryContract
{
    public function store(int $tenantId, int $projectId, StoreSprintData $data): Sprint
    {
        return Sprint::query()->create([
            'tenant_id' => $tenantId,
            'project_id' => $projectId,
            'name' => $data->name,
            'goal' => $data->goal,
            'status' => SprintStatus::Active,
        ]);
    }

    public function findAllByProjectId(int $projectId, int $tenantId): Collection
    {
        return Sprint::query()
            ->where('project_id', $projectId)
            ->where('tenant_id', $tenantId)
            ->orderBy('id')
            ->get();
    }

    public function getSprintById(int $sprintId, int $projectId, int $tenantId): ?Sprint
    {
        return Sprint::query()
            ->whereKey($sprintId)
            ->where('project_id', $projectId)
            ->where('tenant_id', $tenantId)
            ->first();
    }

    public function update(int $tenantId, int $projectId, int $sprintId, StoreSprintData $data): ?Sprint
    {
        $sprint = $this->getSprintById($sprintId, $projectId, $tenantId);

        if ($sprint === null) {
            return null;
        }

        $sprint->update([
            'name' => $data->name,
            'goal' => $data->goal,
            'status' => $data->status,
            'starts_at' => $data->starts_at,
            'ends_at' => $data->ends_at,
        ]);

        return $sprint->fresh();
    }
}
