<?php

declare(strict_types=1);

namespace Application\Tenant\Data;

use Domain\User\Enums\UserRole;
use Domain\User\Enums\UserStatus;
use Spatie\LaravelData\Data;

class TenantMemberCreateData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public UserStatus $status,
        public UserRole $role,
        public string $password,
    ) {}
}
