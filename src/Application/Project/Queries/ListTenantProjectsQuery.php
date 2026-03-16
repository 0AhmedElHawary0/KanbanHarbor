<?php

declare(strict_types=1);

namespace Application\Project\Queries;

use Application\Bus\Query;

final class ListTenantProjectsQuery extends Query
{
    public function __construct(
        private readonly int $tenantId,
    ) {}

    public function getTenantId(): int
    {
        return $this->tenantId;
    }
}
