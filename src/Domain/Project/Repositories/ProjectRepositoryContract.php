<?php

declare(strict_types=1);

namespace Domain\Project\Repositories;

use Application\Project\Data\CreateProjectData;
use Domain\Project\Entities\Project;

interface ProjectRepositoryContract
{
    public function create(int $tenantId, CreateProjectData $data): Project;

    public function findById(int $tenantId, int $projectId): ?Project;
}
