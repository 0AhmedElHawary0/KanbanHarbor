<?php

declare(strict_types=1);

namespace Infrastructure\Tenancy;

use Shared\Tenancy\TenantContext;

final class RequestTenantContext implements TenantContext
{
    private ?int $tenantId = null;

    public function setTenantId(int $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function tenantId(): int
    {
        if ($this->tenantId === null) {
            throw new \RuntimeException('Tenant context has not been resolved for the current request.');
        }

        return $this->tenantId;
    }
}
