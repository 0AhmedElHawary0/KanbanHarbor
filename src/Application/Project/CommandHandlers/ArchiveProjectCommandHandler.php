<?php

declare(strict_types=1);

namespace Application\Project\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\Project\Commands\ArchiveProjectCommand;
use Application\Project\Data\ProjectData;
use Domain\Project\Repositories\ProjectRepositoryContract;
use PhpXmlRpc\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ArchiveProjectCommandHandler extends CommandHandler
{
    public function __construct(private readonly ProjectRepositoryContract $projectRepository) {}

    public function handle(ArchiveProjectCommand $command): ?ProjectData
    {
        $project = $this->projectRepository->archive($command->tenantId, $command->projectId);
        
        if ($project === null) {
            return null;
        }

        return ProjectData::from($project);
    }
}
