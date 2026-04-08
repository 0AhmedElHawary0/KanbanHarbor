<?php

declare(strict_types=1);

namespace Application\Task\Data;

use Carbon\Carbon;
use Domain\Task\Enums\TaskStatus;
use Domain\Task\Enums\TaskPriority;
use Domain\Task\Enums\TaskType;
use Spatie\LaravelData\Data;

class TaskData extends Data
{
    public function __construct(
        public int $id,
        public int $tenant_id,
        public int $project_id,
        public int $sprint_id,
        public string $title,
        public ?string $description = null,
        public ?TaskType $type = null,
        public ?TaskPriority $priority = null,
        public ?TaskStatus $status = null,
        public ?int $story_points = null,
        public ?Carbon $due_date = null,
        public ?int $assignee_id = null,
    ) {}
}
