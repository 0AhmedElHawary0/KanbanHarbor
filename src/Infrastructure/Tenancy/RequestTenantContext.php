<?php

declare(strict_types=1);

namespace Infrastructure\Tenancy;

use Domain\Tenant\Entities\Tenant;
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
        if ($this->tenantId !== null) {
            return $this->tenantId;
        }

        $currentTenant = Tenant::current();

        if ($currentTenant !== null) {
            return (int) $currentTenant->getKey();
        }

        throw new \RuntimeException('Tenant context has not been resolved for the current request.');
    }
}
