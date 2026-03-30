<?php

declare(strict_types=1);

namespace Application\Project\Commands;

use Application\Bus\Command;

final class ArchiveProjectCommand extends Command
{
    public function __construct(
        public int $tenantId,
        public int $projectId,
    ) {}
}
