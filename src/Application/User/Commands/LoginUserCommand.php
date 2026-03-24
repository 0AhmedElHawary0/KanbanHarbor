<?php

declare(strict_types=1);

namespace Application\User\Commands;

use Application\Bus\Command;
use Application\User\Data\UserData;

final class LoginUserCommand extends Command
{
    public function __construct(
        public string $email,
        public string $password,
        public string $device_name
    ) {}
}
