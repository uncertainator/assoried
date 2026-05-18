<?php

namespace App\Enums;

enum ConsultationSource: string
{
    case Numerique = 'numerique';
    case Terrain = 'terrain';

    public function label(): string
    {
        return match ($this) {
            self::Numerique => 'Numérique',
            self::Terrain => 'Terrain',
        };
    }
}
