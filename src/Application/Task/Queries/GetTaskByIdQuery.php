<?php

declare(strict_types=1);

namespace Application\Task\Queries;

use Application\Bus\Query;

final class GetTaskByIdQuery extends Query
{
    public function __construct(
        private readonly int $tenantId,
        private readonly int $taskId,
    ) {}

    public function getTenantId(): int
    {
        return $this->tenantId;
    }

    public function getTaskId(): int
    {
        return $this->taskId;
    }
}
