<?php

declare(strict_types=1);

namespace Shared\Tenancy;

interface TenantContext
{
    public function setTenantId(int $tenantId): void;

    public function tenantId(): int;
}
