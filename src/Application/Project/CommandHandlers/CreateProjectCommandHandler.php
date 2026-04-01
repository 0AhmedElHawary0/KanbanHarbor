<?php

declare(strict_types=1);

namespace Application\Project\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\Project\Commands\CreateProjectCommand;
use Application\Project\Data\ProjectData;
use Domain\Project\Repositories\ProjectRepositoryContract;

final class CreateProjectCommandHandler extends CommandHandler
{
    public function __construct(private readonly ProjectRepositoryContract $projectRepository) {}

    public function handle(CreateProjectCommand $command): ProjectData
    {
        $project = $this->projectRepository->store($command->tenantId, $command->projectData);

        return ProjectData::from($project);
    }
}
