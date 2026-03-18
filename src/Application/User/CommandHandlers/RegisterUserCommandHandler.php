<?php

declare(strict_types=1);

namespace Application\User\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\User\Commands\RegisterUserCommand;
use Application\User\Data\RegisteredUserData;
use Domain\User\Repositories\UserRepositoryContract;

final class RegisterUserCommandHandler extends CommandHandler
{
    public function __construct(private readonly UserRepositoryContract $userRepository) {}

    public function handle(RegisterUserCommand $command): RegisteredUserData
    {
        return $this->userRepository->register($command->userData);
    }
}
