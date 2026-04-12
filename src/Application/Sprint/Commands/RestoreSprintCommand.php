<?php

declare(strict_types=1);

namespace Application\Sprint\Commands;

use Application\Bus\Command;

final class RestoreSprintCommand extends Command
{
    public function __construct(
        public int $tenantId,
        public int $projectId,
        public int $sprintId,
    ) {}
}
