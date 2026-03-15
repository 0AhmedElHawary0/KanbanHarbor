<?php

declare(strict_types=1);

namespace Presentation\Tenancy\Middlewares;

use Closure;
use Domain\Tenant\Entities\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Shared\Tenancy\TenantContext;
use Symfony\Component\HttpFoundation\Response;

final class ResolveTenant
{
    public function __construct(private readonly TenantContext $tenantContext) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->route('tenantId') ?? $request->header('X-Tenant-Id');

        if (! is_numeric($tenantId) || (int) $tenantId < 1) {
            return new JsonResponse([
                'message' => 'A valid tenant context is required.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $tenantId = (int) $tenantId;

        if (! Tenant::query()->whereKey($tenantId)->exists()) {
            return new JsonResponse([
                'message' => 'Tenant not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->tenantContext->setTenantId($tenantId);

        return $next($request);
    }
}
