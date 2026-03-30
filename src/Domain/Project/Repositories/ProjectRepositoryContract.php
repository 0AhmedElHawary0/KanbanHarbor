<?php

declare(strict_types=1);

namespace Domain\Project\Repositories;

use Application\Project\Data\CreateProjectData;
use Domain\Project\Entities\Project;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;

interface ProjectRepositoryContract
{
    public function create(int $tenantId, CreateProjectData $data): Project;

    public function findById(int $tenantId, int $projectId): ?Project;

    public function findAllByTenantId(int $tenantId): Collection;

    public function archive(int $tenantId, int $projectId): ?Project;
}
