<?php

declare(strict_types=1);

namespace Application\Task\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\Task\Commands\StoreTaskCommand;
use Application\Task\Data\StoreTaskData;
use Domain\Sprint\Repositories\SprintRepositoryContract;
use Domain\Task\Repositories\TaskRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class StoreTaskCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly SprintRepositoryContract $sprintRepository,
        private readonly TaskRepositoryContract $taskRepository,
    ) {}

    public function handle(StoreTaskCommand $command): StoreTaskData
    {
        $sprint = $this->sprintRepository->getSprintById($command->tenantId, $command->sprintId);

        if ($sprint === null) {
            throw new ModelNotFoundException('Sprint not found for this tenant.');
        }

        $task = $this->taskRepository->store(
            $command->tenantId,
            $command->sprintId,
            $command->taskData,
        );

        return StoreTaskData::from($task);
    }
}
