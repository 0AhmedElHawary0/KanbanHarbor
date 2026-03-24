<?php

declare(strict_types=1);

namespace Application\User\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\User\Commands\LogoutUserCommand;
use Application\User\Data\LogoutUserData;
use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Validation\ValidationException;

final class LogoutUserCommandHandler extends CommandHandler
{
    public function __construct(private readonly UserRepositoryContract $userRepository) {}

    public function handle(LogoutUserCommand $command): LogoutUserData
    {
        $user = $this->userRepository->findByIdForAuth($command->user_id);

        if ($user === null) {
            throw ValidationException::withMessages([
                'user' => ['Authenticated user not found.']
            ]);
        }

        if ($command->all_devices) {
            $count = $this->userRepository->revokeAllTokens($user);


            return new LogoutUserData(
                message: 'Logged out from all devices.',
                revoked_token_count: $count,
            );
        }

        $revoked = $this->userRepository->revokeCurrentToken($user, $command->current_token_id);

        return new LogoutUserData(
            message: $revoked ? 'Logged out successfully.' : 'No active token was revoked.',
            revoked_token_count: $revoked ? 1 : 0
        );
    }
}
