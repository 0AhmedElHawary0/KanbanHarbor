<?php

declare(strict_types=1);

namespace Application\User\Commands;

use Application\Bus\Command;

final class LogoutUserCommand extends Command
{
    public function __construct(
        public int $user_id,
        public ?int $current_token_id,
        public bool $all_devices = false,
    ) {}
}
