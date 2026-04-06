<?php

declare(strict_types=1);

namespace Application\Sprint\Data;

use Carbon\Carbon;
use Domain\Sprint\Enums\SprintStatus;
use Spatie\LaravelData\Data;

class SprintData extends Data
{
    public int $id;
    public int $tenant_id;
    public int $project_id;
    public string $name;
    public ?string $goal;
    public SprintStatus $status;
    public Carbon $starts_at;
    public Carbon $ends_at;
}
