<?php

declare(strict_types=1);

namespace Presentation\Tenancy\Middlewares;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Shared\Tenancy\TenantContext;
use Spatie\Multitenancy\TenantFinder\TenantFinder;
use Symfony\Component\HttpFoundation\Response;

final class ResolveTenant
{
    public function __construct(
        private readonly TenantContext $tenantContext,
        private readonly TenantFinder $tenantFinder,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->tenantFinder->findForRequest($request);

        if ($tenant === null) {
            return new JsonResponse([
                'message' => 'A valid tenant context is required.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verify authenticated user belongs to this tenant
        if ($request->user() !== null && ! $this->userBelongsToTenant($request, $tenant)) {
            return new JsonResponse([
                'message' => 'You do not have access to this organization.',
            ], Response::HTTP_FORBIDDEN);
        }

        $tenant->makeCurrent();
        $this->tenantContext->setTenantId((int) data_get($tenant, 'id'));

        return $next($request);
    }

    private function userBelongsToTenant(Request $request, mixed $tenant): bool
    {
        $user = $request->user();

        if ($user === null) {
            return true; // Unauthenticated is allowed here; auth middleware will catch later
        }

        return $user->tenants()
            ->whereKey((int) data_get($tenant, 'id'))
            ->exists();
    }
}
