<?php

namespace App\Enums;

enum ScrutinMajorityType: string
{
    case Simple = 'simple';
    case Qualified = 'qualified';

    public function label(): string
    {
        return match ($this) {
            self::Simple => 'Majorité simple (> 50 %)',
            self::Qualified => 'Majorité qualifiée (seuil configurable)',
        };
    }
}
