<?php

declare(strict_types=1);

namespace Application\Tenant\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\Tenant\Commands\CreateTenantCommand;
use Domain\Tenant\Repositories\TenantRepositoryContract;

final class CreateTenantCommandHandler extends CommandHandler
{
    public function __construct(private readonly TenantRepositoryContract $tenantRepository) {}

    public function handle(CreateTenantCommand $command): int
    {
        return $this->tenantRepository->create($command->tenantData)->id;
    }
}
