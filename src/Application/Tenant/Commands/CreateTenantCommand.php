<?php

declare(strict_types=1);

namespace Application\Tenant\Commands;

use Application\Bus\Command;
use Application\Tenant\Data\CreateTenantData;

final class CreateTenantCommand extends Command
{
    public function __construct(
        public CreateTenantData $tenantData,
    ) {}
}
