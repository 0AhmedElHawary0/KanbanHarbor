<?php

declare(strict_types=1);

namespace Application\Tenant\Data;

use Domain\User\Enums\UserRole;
use Spatie\LaravelData\Data;

class TenantMemberCreateData extends Data
{
    public function __construct(
        public string $email,
        public UserRole $role,
    ) {}
}
