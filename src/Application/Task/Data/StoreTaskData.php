<?php

declare(strict_types=1);

namespace Application\Task\Data;

use Carbon\Carbon;
use Domain\Task\Enums\TaskStatus;
use Domain\Task\Enums\TaskPriority;
use Domain\Task\Enums\TaskType;
use Spatie\LaravelData\Data;

class StoreTaskData extends Data
{
    public function __construct(
        public string $title,
        public ?string $description = null,
        public ?TaskType $task_type,
        public ?TaskPriority $task_priority,
        public ?TaskStatus $task_status,
        public ?int $story_points,
        public ?Carbon $due_date,
        public ?int $assignee_id,
    ) {}
}
