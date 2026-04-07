<?php

declare(strict_types=1);

namespace Application\Sprint\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\Sprint\Commands\UpdateSprintCommand;
use Application\Sprint\Data\StoreSprintData;
use Domain\Project\Repositories\ProjectRepositoryContract;
use Domain\Sprint\Repositories\SprintRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class UpdateSprintCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly ProjectRepositoryContract $projectRepository,
        private readonly SprintRepositoryContract $sprintRepository,
    ) {}

    public function handle(UpdateSprintCommand $command): StoreSprintData
    {
        $project = $this->projectRepository->findById($command->tenantId, $command->projectId);

        if ($project === null) {
            throw new ModelNotFoundException('Project not found for this tenant.');
        }

        $sprint = $this->sprintRepository->update(
            $command->tenantId,
            $command->projectId,
            $command->sprintId,
            $command->sprintData,
        );

        if ($sprint === null) {
            throw new ModelNotFoundException('Sprint not found for this project.');
        }

        return StoreSprintData::from($sprint);
    }
}
