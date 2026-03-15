<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Query;

class GetUserByIdQuery extends Query
{
    public function __construct(
        private readonly int $id,
        private readonly int $tenantId,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getTenantId(): int
    {
        return $this->tenantId;
    }
}
