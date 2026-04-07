<?php

declare(strict_types=1);

namespace Application\Sprint\QueryHandlers;

use Application\Bus\QueryHandler;
use Application\Sprint\Queries\GetSprintByIdQuery;
use Domain\Project\Repositories\ProjectRepositoryContract;
use Domain\Sprint\Entities\Sprint;
use Domain\Sprint\Repositories\SprintRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class GetSprintByIdQueryHandler extends QueryHandler
{
    public function __construct(
        private readonly SprintRepositoryContract $sprintRepository,
        private readonly ProjectRepositoryContract $projectRepository,
    ) {}

    public function handle(GetSprintByIdQuery $query): Sprint
    {
        $project = $this->projectRepository->findById($query->getTenantId(), $query->getProjectId());

        if ($project === null) {
            throw new ModelNotFoundException('Project not found for this tenant.');
        }

        $sprint = $this->sprintRepository->getSprintById(
            $query->getSprintId(),
            $query->getProjectId(),
            $query->getTenantId()
        );

        if ($sprint === null) {
            throw new ModelNotFoundException('Sprint not found for this project and tenant.');
        }

        return $sprint;
    }
}
