<?php

declare(strict_types=1);

namespace Application\Project\QueryHandlers;

use Application\Bus\QueryHandler;
use Application\Project\Queries\ListTenantProjectsQuery;
use Domain\Project\Repositories\ProjectRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

final class ListTenantProjectsQueryHandler extends QueryHandler
{
    public function __construct(private readonly ProjectRepositoryContract $projectRepository) {}

    public function handle(ListTenantProjectsQuery $query): Collection
    {
        return $this->projectRepository->findAllByTenantId($query->getTenantId());
    }
}
