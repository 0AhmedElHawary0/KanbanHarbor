<?php

declare(strict_types=1);

namespace Infrastructure\Task\Persistence\Repositories;

use Application\Task\Data\StoreTaskData;
use Domain\Task\Entities\Task;
use Domain\Task\Enums\TaskStatus;
use Domain\Task\Enums\TaskPriority;
use Domain\Task\Enums\TaskType;
use Domain\Task\Repositories\TaskRepositoryContract;

final class TaskRepository implements TaskRepositoryContract
{
    public function store(int $tenantId, int $sprintId, StoreTaskData $data): Task
    {
        return Task::query()->create([
            'tenant_id' => $tenantId,
            'sprint_id' => $sprintId,
            'title' => $data->title,
            'description' => $data->description,
            'priority' => $data->task_priority ?? TaskPriority::Medium,
            'status' => $data->task_status ?? TaskStatus::ToDo,
            'type' => $data->task_type ?? TaskType::Feature,
            'story_points' => $data->story_points,
            'assignee_id' => $data->assignee_id,
            'due_date' => $data->due_date,
        ]);
    }
}
