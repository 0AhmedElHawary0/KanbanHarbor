<?php

declare(strict_types=1);

namespace Application\Project\QueryHandlers;

use Application\Bus\QueryHandler;
use Application\Project\Queries\GetProjectByIdQuery;
use Domain\Project\Repositories\ProjectRepositoryContract;

final class GetProjectByIdQueryHandler extends QueryHandler
{
    public function __construct(private readonly ProjectRepositoryContract $projectRepository) {}

    public function handle(GetProjectByIdQuery $query): ?object
    {
        return $this->projectRepository->findById($query->getTenantId(), $query->getProjectId());
    }
}
