<?php

declare(strict_types=1);

namespace Domain\Task\Repositories;

use Application\Task\Data\StoreTaskData;
use Application\Task\Data\UpdateTaskData;
use Domain\Task\Entities\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryContract
{
    public function store(int $tenantId, int $projectId, int $sprintId, StoreTaskData $data): Task;

    public function listSprintTasks(int $tenantId, int $sprintId): Collection;

    public function getTaskById(int $tenantId, int $taskId): ?Task;

    public function update(int $tenantId, int $taskId, UpdateTaskData $data): ?Task;

    public function hardDelete(int $tenantId, int $taskId): bool;
}
