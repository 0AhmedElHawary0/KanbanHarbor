<?php

declare(strict_types=1);

namespace Application\Tenant\Queries;

use Application\Bus\Query;

final class ListTenantMembersQuery extends Query
{
    public function __construct(private readonly int $tenantId) {}

    public function getTenantId(): int
    {
        return $this->tenantId;
    }
}
