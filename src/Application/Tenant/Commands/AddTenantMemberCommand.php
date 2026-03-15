<?php

declare(strict_types=1);

namespace Application\Tenant\Commands;

use Application\Bus\Command;
use Application\Tenant\Data\TenantMemberCreateData;

final class AddTenantMemberCommand extends Command
{
    public function __construct(
        public int $tenantId,
        public TenantMemberCreateData $memberData,
    ) {}
}
