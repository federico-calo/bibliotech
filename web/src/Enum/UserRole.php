<?php

namespace App\Enum;

enum UserRole: string
{

    case DEFAULT = 'member';
    case ADMIN = 'administrator';

    public function getDescription(): string
    {
        return match ($this) {
            self::DEFAULT => 'This is the default user role.',
            self::ADMIN => 'This is the administrator role.',
        };
    }

}
