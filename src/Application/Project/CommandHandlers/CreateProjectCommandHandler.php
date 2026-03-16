<?php

declare(strict_types=1);

namespace Application\Project\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\Project\Commands\CreateProjectCommand;
use Domain\Project\Repositories\ProjectRepositoryContract;

final class CreateProjectCommandHandler extends CommandHandler
{
    public function __construct(private readonly ProjectRepositoryContract $projectRepository) {}

    public function handle(CreateProjectCommand $command): int
    {
        return $this->projectRepository->create($command->tenantId, $command->projectData)->id;
    }
}
