<?php

declare(strict_types=1);

namespace Application\Project\Data;

use Carbon\Carbon;
use Domain\Project\Enums\ProjectStatus;
use Spatie\LaravelData\Data;

class ProjectData extends Data
{
    public int $id;
    public int $tenant_id;
    public string $name;
    public ?string $description;
    public ProjectStatus $status;
    public Carbon $created_at;
    public Carbon $updated_at;
}
