<?php

declare(strict_types=1);

namespace Application\Sprint\Queries;

use Application\Bus\Query;

final class GetSprintByIdQuery extends Query
{
    public function __construct(
        private readonly int $tenantId,
        private readonly int $projectId,
        private readonly int $sprintId,
    ) {}

    public function getTenantId(): int
    {
        return $this->tenantId;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getSprintId(): int
    {
        return $this->sprintId;
    }
}
