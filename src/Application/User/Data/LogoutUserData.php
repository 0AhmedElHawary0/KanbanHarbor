<?php

declare(strict_types=1);

namespace Application\User\Data;


use Spatie\LaravelData\Data;

final class LogoutUserData extends Data
{
    public function __construct(
        public string $message,
        public int $revoked_token_count,
    ) {}
}
