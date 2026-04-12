<?php

declare(strict_types=1);

namespace Application\Sprint\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\Sprint\Commands\RestoreSprintCommand;
use Domain\Project\Repositories\ProjectRepositoryContract;
use Domain\Sprint\Repositories\SprintRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class RestoreSprintCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly ProjectRepositoryContract $projectRepository,
        private readonly SprintRepositoryContract $sprintRepository,
    ) {}

    public function handle(RestoreSprintCommand $command): bool
    {
        $project = $this->projectRepository->findById($command->tenantId, $command->projectId);

        if ($project === null) {
            throw new ModelNotFoundException('Project not found for this tenant.');
        }

        return $this->sprintRepository->restore(
            $command->tenantId,
            $command->projectId,
            $command->sprintId
        );
    }
}
