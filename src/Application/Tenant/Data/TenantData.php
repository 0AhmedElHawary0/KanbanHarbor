<?php

declare(strict_types=1);

namespace Application\Tenant\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class TenantData extends Data
{
    public int $id;
    public string $name;
    public string $slug;
    public Carbon $created_at;
    public Carbon $updated_at;
}
