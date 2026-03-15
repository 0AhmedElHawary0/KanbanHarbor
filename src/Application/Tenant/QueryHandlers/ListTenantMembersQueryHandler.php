<?php

declare(strict_types=1);

namespace Application\Tenant\QueryHandlers;

use Application\Bus\QueryHandler;
use Application\Tenant\Queries\ListTenantMembersQuery;
use Domain\Tenant\Repositories\TenantRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

final class ListTenantMembersQueryHandler extends QueryHandler
{
    public function __construct(private readonly TenantRepositoryContract $tenantRepository) {}

    public function handle(ListTenantMembersQuery $query): Collection
    {
        return $this->tenantRepository->listMembers($query->getTenantId());
    }
}
