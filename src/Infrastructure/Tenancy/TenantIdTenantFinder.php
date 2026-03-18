<?php

declare(strict_types=1);

namespace Infrastructure\Tenancy;

use Domain\Tenant\Entities\Tenant;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

final class TenantIdTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?IsTenant
    {
        $tenantId = $this->resolveTenantId($request);

        if ($tenantId === null) {
            return null;
        }

        return Tenant::query()->find($tenantId);
    }

    private function resolveTenantId(Request $request): ?int
    {
        $headerTenantId = $request->header('X-Tenant-Id');

        if (is_numeric($headerTenantId) && (int) $headerTenantId > 0) {
            return (int) $headerTenantId;
        }

        if (preg_match('~(?:^|/)tenants/(\d+)(?:/|$)~', $request->path(), $matches) !== 1) {
            return null;
        }

        return (int) $matches[1];
    }
}
