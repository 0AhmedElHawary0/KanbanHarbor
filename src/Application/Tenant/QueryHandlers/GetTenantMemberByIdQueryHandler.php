<?php

declare(strict_types=1);

namespace Application\Tenant\QueryHandlers;

use Application\Bus\QueryHandler;
use Application\Tenant\Queries\GetTenantMemberByIdQuery;
use Domain\Tenant\Repositories\TenantRepositoryContract;

final class GetTenantMemberByIdQueryHandler extends QueryHandler
{
    public function __construct(private readonly TenantRepositoryContract $tenantRepository) {}

    public function handle(GetTenantMemberByIdQuery $query): ?object
    {
        return $this->tenantRepository->findMemberById($query->getTenantId(), $query->getUserId());
    }
}
