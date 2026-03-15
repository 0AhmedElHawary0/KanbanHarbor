<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Query;

class GetUserByEmailQuery extends Query
{
    public function __construct(
        private readonly string $email,
        private readonly int $tenantId,
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTenantId(): int
    {
        return $this->tenantId;
    }
}
