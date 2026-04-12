<?php

declare(strict_types=1);

namespace Infrastructure\Task\Persistence\Repositories;

use Application\Task\Data\StoreTaskData;
use Application\Task\Data\UpdateTaskData;
use Domain\Task\Entities\Task;
use Domain\Task\Enums\TaskStatus;
use Domain\Task\Enums\TaskPriority;
use Domain\Task\Enums\TaskType;
use Domain\Task\Repositories\TaskRepositoryContract;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Optional;

final class TaskRepository implements TaskRepositoryContract
{
    public function store(int $tenantId, int $projectId, int $sprintId, StoreTaskData $data): Task
    {
        return Task::query()->create([
            'tenant_id' => $tenantId,
            'project_id' => $projectId,
            'sprint_id' => $sprintId,
            'title' => $data->title,
            'description' => $data->description,
            'priority' => $data->priority ?? TaskPriority::Medium,
            'status' => $data->status ?? TaskStatus::ToDo,
            'type' => $data->type ?? TaskType::Feature,
            'story_points' => $data->story_points,
            'assignee_id' => $data->assignee_id,
            'due_date' => $data->due_date,
        ]);
    }

    public function listSprintTasks(int $tenantId, int $sprintId): Collection
    {
        return Task::query()
            ->where('tenant_id', $tenantId)
            ->where('sprint_id', $sprintId)
            ->orderBy('id')
            ->get();
    }

    public function getTaskById(int $tenantId, int $taskId): ?Task
    {
        return Task::query()
            ->whereKey($taskId)
            ->where('tenant_id', $tenantId)
            ->first();
    }

    public function update(int $tenantId, int $taskId, UpdateTaskData $data): ?Task
    {
        $task = $this->getTaskById($tenantId, $taskId);
        if ($task === null) {
            return null;
        }

        $payload = [];

        if (! $data->title instanceof Optional) {
            $payload['title'] = $data->title;
        }

        if (! $data->description instanceof Optional) {
            $payload['description'] = $data->description;
        }

        if (! $data->type instanceof Optional) {
            $payload['type'] = $data->type;
        }

        if (! $data->priority instanceof Optional) {
            $payload['priority'] = $data->priority;
        }

        if (! $data->status instanceof Optional) {
            $payload['status'] = $data->status;
        }

        if (! $data->story_points instanceof Optional) {
            $payload['story_points'] = $data->story_points;
        }

        if (! $data->assignee_id instanceof Optional) {
            $payload['assignee_id'] = $data->assignee_id;
        }

        if (! $data->due_date instanceof Optional) {
            $payload['due_date'] = $data->due_date;
        }

        if ($payload !== []) {
            $task->update($payload);
        }

        return $task->refresh();
    }

    public function hardDelete(int $tenantId, int $taskId): bool
    {
        $task = $this->getTaskById($tenantId, $taskId);

        if ($task === null) {
            return false;
        }

        return (bool) $task->forceDelete();
    }
}
