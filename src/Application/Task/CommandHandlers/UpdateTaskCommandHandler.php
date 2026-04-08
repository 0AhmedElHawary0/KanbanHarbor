<?php

declare(strict_types=1);

namespace Application\Task\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\Task\Commands\UpdateTaskCommand;
use Application\Task\Data\TaskData;
use Domain\Task\Repositories\TaskRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class UpdateTaskCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly TaskRepositoryContract $taskRepository,
    ) {}

    public function handle(UpdateTaskCommand $command): TaskData
    {
        $task = $this->taskRepository->getTaskById($command->tenantId, $command->taskId);

        if ($task === null) {
            throw new ModelNotFoundException('Task not found.');
        }

        $task = $this->taskRepository->update(
            $command->tenantId,
            $command->taskId,
            $command->data,
        );

        return TaskData::from($task);
    }
}
