<?php

declare(strict_types=1);

namespace Application\User\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\User\Commands\UpdateUserCommand;
use Domain\User\Repositories\UserRepositoryContract;

final class UpdateUserCommandHandler extends CommandHandler
{
    public function __construct(private readonly UserRepositoryContract $userRepository) {}

    public function handle(UpdateUserCommand $command): bool
    {
        return $this->userRepository->update($command->id, $command->userData, $command->tenantId);
    }
}
