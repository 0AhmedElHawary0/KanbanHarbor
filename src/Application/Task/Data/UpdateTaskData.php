<?php

declare(strict_types=1);

namespace Application\Task\Data;

use Carbon\Carbon;
use Domain\Task\Enums\TaskPriority;
use Domain\Task\Enums\TaskStatus;
use Domain\Task\Enums\TaskType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class UpdateTaskData extends Data
{
    public function __construct(
        public Optional|string|null $title,
        public Optional|string|null $description,
        public Optional|TaskType|null $type,
        public Optional|TaskPriority|null $priority,
        public Optional|TaskStatus|null $status,
        public Optional|int|null $story_points,
        public Optional|Carbon|null $due_date,
        public Optional|int|null $assignee_id,
    ) {}
}
