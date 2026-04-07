<?php

declare(strict_types=1);

namespace Application\Sprint\Commands;

use Application\Bus\Command;
use Application\Sprint\Data\StoreSprintData;

final class UpdateSprintCommand extends Command
{
    public function __construct(
        public int $tenantId,
        public int $projectId,
        public int $sprintId,
        public StoreSprintData $sprintData,
    ) {}
}
