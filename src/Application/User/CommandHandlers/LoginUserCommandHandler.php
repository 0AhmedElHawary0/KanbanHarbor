<?php

declare(strict_types=1);

namespace Application\User\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\User\Commands\LoginUserCommand;
use Application\User\Data\LoginUserData;
use Application\User\Data\RegisteredUserData;
use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class LoginUserCommandHandler extends CommandHandler
{
    public function __construct(private readonly UserRepositoryContract $userRepository) {}

    public function handle(LoginUserCommand $command): LoginUserData
    {
        $user = $this->userRepository->findByEmailForAuth($command->email);

        if ($user === null || !Hash::check($command->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ["Invalid Credentials."]
            ]);
        }

        if (! $user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Email is not verified. Please verify your email before logging in.'],
            ]);
        }

        $token = $this->userRepository->createApiToken(
            $user,
            $command->device_name !== '' ? $command->device_name : 'api',
        );

        return new LoginUserData(
            access_token: $token,
            token_type: 'Bearer',
            user: RegisteredUserData::from($user)
        );
    }
}
