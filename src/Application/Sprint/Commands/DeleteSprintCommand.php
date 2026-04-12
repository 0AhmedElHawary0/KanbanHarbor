<?php

declare(strict_types=1);

namespace Application\Sprint\Commands;

use Application\Bus\Command;

final class DeleteSprintCommand extends Command
{
    public function __construct(
        public int $tenantId,
        public int $projectId,
        public int $sprintId,
    ) {}
}
