<?php

declare(strict_types=1);

namespace Application\Task\Commands;

use Application\Bus\Command;
use Application\Task\Data\StoreTaskData;

final class StoreTaskCommand extends Command
{
    public function __construct(
        public int $tenantId,
        public int $sprintId,
        public StoreTaskData $taskData,
    ) {}
}
