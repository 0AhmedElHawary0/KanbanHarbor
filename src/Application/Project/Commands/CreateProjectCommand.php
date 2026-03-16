<?php

declare(strict_types=1);

namespace Application\Project\Commands;

use Application\Bus\Command;
use Application\Project\Data\CreateProjectData;

final class CreateProjectCommand extends Command
{
    public function __construct(
        public int $tenantId,
        public CreateProjectData $projectData,
    ) {}
}
