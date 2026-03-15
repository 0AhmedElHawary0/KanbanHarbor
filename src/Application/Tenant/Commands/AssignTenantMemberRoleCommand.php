<?php

declare(strict_types=1);

namespace Application\Tenant\Commands;

use Application\Bus\Command;
use Domain\User\Enums\UserRole;

final class AssignTenantMemberRoleCommand extends Command
{
    public function __construct(
        public int $tenantId,
        public int $userId,
        public UserRole $role,
    ) {}
}
