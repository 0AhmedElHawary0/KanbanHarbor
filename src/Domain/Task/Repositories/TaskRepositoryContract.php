<?php

declare(strict_types=1);

namespace Domain\Task\Repositories;

use Application\Task\Data\StoreTaskData;
use Domain\Task\Entities\Task;

interface TaskRepositoryContract
{
    public function store(int $tenantId, int $sprintId, StoreTaskData $data): Task;
}
