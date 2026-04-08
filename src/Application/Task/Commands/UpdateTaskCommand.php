<?php

declare(strict_types=1);

namespace Application\Task\Commands;

use Application\Bus\Command;
use Application\Task\Data\UpdateTaskData;

final class UpdateTaskCommand extends Command
{
    public function __construct(
        public int $tenantId,
        public int $taskId,
        public UpdateTaskData $data,
    ) {}
}
