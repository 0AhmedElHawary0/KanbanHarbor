<?php

declare(strict_types=1);

namespace Application\Tenant\QueryHandlers;

use Application\Bus\QueryHandler;
use Application\Tenant\Queries\GetTenantByIdQuery;
use Domain\Tenant\Repositories\TenantRepositoryContract;

final class GetTenantByIdQueryHandler extends QueryHandler
{
    public function __construct(private readonly TenantRepositoryContract $tenantRepository) {}

    public function handle(GetTenantByIdQuery $query): ?object
    {
        return $this->tenantRepository->findById($query->getTenantId());
    }
}
