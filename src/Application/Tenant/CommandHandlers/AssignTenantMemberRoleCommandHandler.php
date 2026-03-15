<?php

declare(strict_types=1);

namespace Application\Tenant\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\Tenant\Commands\AssignTenantMemberRoleCommand;
use Domain\Tenant\Repositories\TenantRepositoryContract;

final class AssignTenantMemberRoleCommandHandler extends CommandHandler
{
    public function __construct(private readonly TenantRepositoryContract $tenantRepository) {}

    public function handle(AssignTenantMemberRoleCommand $command): ?int
    {
        return $this->tenantRepository
            ->assignMemberRole($command->tenantId, $command->userId, $command->role)?->id;
    }
}
