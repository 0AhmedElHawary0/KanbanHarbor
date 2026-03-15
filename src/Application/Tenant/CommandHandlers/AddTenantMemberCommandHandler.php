<?php

declare(strict_types=1);

namespace Application\Tenant\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\Tenant\Commands\AddTenantMemberCommand;
use Domain\Tenant\Repositories\TenantRepositoryContract;

final class AddTenantMemberCommandHandler extends CommandHandler
{
    public function __construct(private readonly TenantRepositoryContract $tenantRepository) {}

    public function handle(AddTenantMemberCommand $command): int
    {
        return $this->tenantRepository->addMember($command->tenantId, $command->memberData)->id;
    }
}
