<?php

declare(strict_types=1);

namespace Application\User\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\User\Commands\RegisterUserCommand;
use Application\User\Data\RegisteredUserData;
use Application\User\Events\UserRegistered;
use Domain\User\Repositories\UserRepositoryContract;

final class RegisterUserCommandHandler extends CommandHandler
{
    public function __construct(private readonly UserRepositoryContract $userRepository) {}

    public function handle(RegisterUserCommand $command): RegisteredUserData
    {
        $registeredUser = $this->userRepository->register($command->userData);

        event(new UserRegistered($registeredUser->id));

        return $registeredUser;
    }
}
