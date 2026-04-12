<?php

declare(strict_types=1);

namespace Application\User\Events;

final class UserRegistered
{
    public function __construct(public readonly int $userId) {}
}
