<?php

declare(strict_types=1);

namespace Application\Task\Commands;

use Application\Bus\Command;

final class DeleteTaskCommand extends Command
{
    public function __construct(
        public int $tenantId,
        public int $taskId,
    ) {}
}
