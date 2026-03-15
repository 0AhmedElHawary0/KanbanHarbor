<?php

declare(strict_types=1);

namespace Domain\User\Enums;

enum UserRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
