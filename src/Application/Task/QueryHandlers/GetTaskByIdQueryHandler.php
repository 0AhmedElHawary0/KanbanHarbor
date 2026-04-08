<?php

declare(strict_types=1);

namespace Application\Task\QueryHandlers;

use Application\Bus\QueryHandler;
use Application\Task\Queries\GetTaskByIdQuery;
use Domain\Task\Entities\Task;
use Domain\Task\Repositories\TaskRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class GetTaskByIdQueryHandler extends QueryHandler
{
    public function __construct(private readonly TaskRepositoryContract $taskRepository) {}

    public function handle(GetTaskByIdQuery $query): ?Task
    {
        $task = $this->taskRepository->getTaskById($query->getTenantId(), $query->getTaskId());

        if ($task === null) {
            throw new ModelNotFoundException("The task required couldn't be found.");
        }

        return $task;
    }
}
