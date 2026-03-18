<?php

declare(strict_types=1);

namespace Application\User\Commands;

use Application\Bus\Command;
use Application\User\Data\UserData;

final class UpdateUserCommand extends Command
{
    public function __construct(
        public int $id,
        public UserData $userData,
        public int $tenantId,
    ) {}
}
