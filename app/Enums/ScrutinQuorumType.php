<?php

namespace App\Enums;

enum ScrutinQuorumType: string
{
    case Fixed = 'fixed';
    case Proportional = 'proportional';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Fixe (nombre de membres)',
            self::Proportional => 'Proportionnel (% des membres actifs)',
        };
    }
}
