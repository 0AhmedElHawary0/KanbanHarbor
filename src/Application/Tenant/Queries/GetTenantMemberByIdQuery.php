<?php

declare(strict_types=1);

namespace Application\Tenant\Queries;

use Application\Bus\Query;

final class GetTenantMemberByIdQuery extends Query
{
    public function __construct(
        private readonly int $tenantId,
        private readonly int $userId,
    ) {}

    public function getTenantId(): int
    {
        return $this->tenantId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
