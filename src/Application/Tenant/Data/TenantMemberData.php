<?php

declare(strict_types=1);

namespace Application\Tenant\Data;

use Carbon\Carbon;
use Domain\User\Enums\UserRole;
use Domain\User\Enums\UserStatus;
use Spatie\LaravelData\Data;

class TenantMemberData extends Data
{
    public int $id;
    public int $tenant_id;
    public string $name;
    public string $email;
    public UserStatus $status;
    public UserRole $role;
    public Carbon $created_at;
    public Carbon $updated_at;
}
