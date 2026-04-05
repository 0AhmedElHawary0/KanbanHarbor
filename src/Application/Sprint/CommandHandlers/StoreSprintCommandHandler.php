<?php

declare(strict_types=1);

namespace Application\Sprint\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\Sprint\Commands\StoreSprintCommand;
use Application\Sprint\Data\StoreSprintData;
use Domain\Project\Repositories\ProjectRepositoryContract;
use Domain\Sprint\Repositories\SprintRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class StoreSprintCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly ProjectRepositoryContract $projectRepository,
        private readonly SprintRepositoryContract $sprintRepository,
    ) {}

    public function handle(StoreSprintCommand $command): StoreSprintData
    {
        $project = $this->projectRepository->findById($command->tenantId, $command->projectId);

        if ($project === null) {
            throw new ModelNotFoundException('Project not found for this tenant.');
        }

        $sprint = $this->sprintRepository->store(
            $command->tenantId,
            $command->projectId,
            $command->sprintData,
        );

        return StoreSprintData::from($sprint);
    }
}
