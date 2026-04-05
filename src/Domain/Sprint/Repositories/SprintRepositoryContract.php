<?php

declare(strict_types=1);

namespace Domain\Sprint\Repositories;

use Application\Sprint\Data\StoreSprintData;
use Domain\Sprint\Entities\Sprint;

interface SprintRepositoryContract
{
    public function store(int $tenantId, int $projectId, StoreSprintData $data): Sprint;
}
