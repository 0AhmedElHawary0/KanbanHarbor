<?php

declare(strict_types=1);

namespace Application\Sprint\QueryHandlers;

use Application\Bus\QueryHandler;
use Application\Sprint\Queries\ListProjectSprintsQuery;
use Domain\Project\Repositories\ProjectRepositoryContract;
use Domain\Sprint\Repositories\SprintRepositoryContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class ListProjectSprintsQueryHandler extends QueryHandler
{
    public function __construct(
        private readonly SprintRepositoryContract $sprintRepository,
        private readonly ProjectRepositoryContract $projectRepository,
    ) {}

    public function handle(ListProjectSprintsQuery $query): Collection
    {
        $project = $this->projectRepository->findById($query->getTenantId(), $query->getProjectId());

        if ($project === null) {
            throw new ModelNotFoundException('Project not found for this tenant.');
        }

        return $this->sprintRepository->findAllByProjectId($query->getProjectId(), $query->getTenantId());
    }
}
