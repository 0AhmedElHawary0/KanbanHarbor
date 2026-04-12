<?php

declare(strict_types=1);

namespace Application\Task\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\Task\Commands\DeleteTaskCommand;
use Domain\Task\Repositories\TaskRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class DeleteTaskCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly TaskRepositoryContract $taskRepository,
    ) {}

    public function handle(DeleteTaskCommand $command): bool
    {
        $task = $this->taskRepository->getTaskById($command->tenantId, $command->taskId);

        if ($task === null) {
            throw new ModelNotFoundException("Task not found.");
        }

        return $this->taskRepository->hardDelete($command->tenantId, $command->taskId);
    }
}
