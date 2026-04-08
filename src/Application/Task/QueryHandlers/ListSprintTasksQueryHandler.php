<?php

declare(strict_types=1);

namespace Application\Task\QueryHandlers;

use Application\Bus\QueryHandler;
use Application\Task\Queries\ListSprintTasksQuery;
use Domain\Sprint\Repositories\SprintRepositoryContract;
use Domain\Task\Repositories\TaskRepositoryContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class ListSprintTasksQueryHandler extends QueryHandler
{
    public function __construct(
        private readonly TaskRepositoryContract $taskRepository,
        private readonly SprintRepositoryContract $sprintRepository,
    ) {}

    public function handle(ListSprintTasksQuery $query): Collection
    {
        $sprint = $this->sprintRepository->getSprintById($query->getTenantId(), $query->getSprintId());

        if ($sprint === null) {
            throw new ModelNotFoundException("The sprint requested isn't available in this tenant.");
        }

        return $this->taskRepository->listSprintTasks($query->getTenantId(), $query->getSprintId());
    }
}
