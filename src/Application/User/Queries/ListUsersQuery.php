<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Query;

final class ListUsersQuery extends Query
{
    public function __construct(private readonly int $tenantId) {}

    public function getTenantId(): int
    {
        return $this->tenantId;
    }
}
