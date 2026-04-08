<?php

declare(strict_types=1);

namespace Application\Task\Queries;

use Application\Bus\Query;

final class ListSprintTasksQuery extends Query
{
    public function __construct(
        private readonly int $tenantId,
        private readonly int $sprintId,
    ) {}

    public function getTenantId(): int
    {
        return $this->tenantId;
    }

    public function getSprintId(): int
    {
        return $this->sprintId;
    }
}
