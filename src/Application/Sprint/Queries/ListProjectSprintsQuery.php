<?php

declare(strict_types=1);

namespace Application\Sprint\Queries;

use Application\Bus\Query;

final class ListProjectSprintsQuery extends Query
{
    public function __construct(
        private readonly int $tenantId,
        private readonly int $projectId,
    ) {}

    public function getTenantId(): int
    {
        return $this->tenantId;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }
}
