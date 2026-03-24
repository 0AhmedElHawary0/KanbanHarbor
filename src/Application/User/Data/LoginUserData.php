<?php

declare(strict_types=1);

namespace Application\User\Data;


use Spatie\LaravelData\Data;

final class LoginUserData extends Data
{
    public function __construct(
        public string $access_token,
        public string $token_type,
        public RegisteredUserData $user,
    ) {}
}
