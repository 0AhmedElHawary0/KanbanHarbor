<?php

declare(strict_types=1);

namespace Application\Sprint\Data;

use Domain\Sprint\Enums\SprintStatus;
use Spatie\LaravelData\Data;

class StoreSprintData extends Data
{
    public function __construct(
        public string $name,
        public ?string $goal = null,
        public ?SprintStatus $status = null,
        public ?string $starts_at = null,
        public ?string $ends_at = null,
    ) {}
}
