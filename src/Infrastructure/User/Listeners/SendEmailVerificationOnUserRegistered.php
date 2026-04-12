<?php

declare(strict_types=1);

namespace Infrastructure\User\Listeners;

use Application\User\Events\UserRegistered;
use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

final class SendEmailVerificationOnUserRegistered implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'mail';

    public function __construct(private readonly UserRepositoryContract $userRepository) {}

    public function handle(UserRegistered $event): void
    {
        $user = $this->userRepository->findByIdForAuth($event->userId);

        if ($user === null || $user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }
}
