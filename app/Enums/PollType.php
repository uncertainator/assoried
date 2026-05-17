<?php

namespace App\Enums;

enum PollType: string
{
    case YesNo = 'yes_no';
    case Multiple = 'multiple';

    public function label(): string
    {
        return match ($this) {
            self::YesNo => 'Oui / Non',
            self::Multiple => 'Choix multiple',
        };
    }
}
