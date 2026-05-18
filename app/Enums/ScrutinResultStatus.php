<?php

namespace App\Enums;

enum ScrutinResultStatus: string
{
    case QuorumNotReached = 'quorum_not_reached';
    case Adopted = 'adopted';
    case NoDecision = 'no_decision';

    public function label(): string
    {
        return match ($this) {
            self::QuorumNotReached => 'Nul — quorum non atteint',
            self::Adopted => 'Adopté',
            self::NoDecision => 'Sans décision',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::QuorumNotReached => 'fb-badge-brique',
            self::Adopted => 'fb-badge-mousse',
            self::NoDecision => 'fb-badge-ocre',
        };
    }
}
