<?php

declare(strict_types=1);

namespace Infrastructure\Authorization;

use Illuminate\Database\Eloquent\Model;
use Shared\Tenancy\TenantContext;
use Spatie\Permission\Contracts\PermissionsTeamResolver;

final class TenantPermissionTeamResolver implements PermissionsTeamResolver
{
    private int|string|null $tenantId = null;

    public function getPermissionsTeamId(): int|string|null
    {
        if ($this->tenantId !== null) {
            return $this->tenantId;
        }

        try {
            /** @var TenantContext $tenantContext */
            $tenantContext = app(TenantContext::class);

            return $tenantContext->tenantId();
        } catch (\Throwable) {
            return null;
        }
    }

    public function setPermissionsTeamId(int|string|Model|null $id): void
    {
        if ($id instanceof Model) {
            $id = $id->getKey();
        }

        $this->tenantId = $id;
    }
}
