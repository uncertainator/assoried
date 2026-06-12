<?php

namespace App\Enums;

enum UserRole: string
{
    case Superadmin = 'superadmin';
    case Admin = 'admin';
    case Referent = 'referent';
    case Adherent = 'adherent';

    public function label(): string
    {
        return match ($this) {
            self::Superadmin => 'Super-administrateur',
            self::Admin => 'Administrateur',
            self::Referent => 'Référent',
            self::Adherent => 'Adhérent',
        };
    }
}
