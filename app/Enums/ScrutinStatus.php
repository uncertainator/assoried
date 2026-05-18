<?php

namespace App\Enums;

enum ScrutinStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case Closed = 'closed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Open => 'Ouvert',
            self::Closed => 'Clôturé',
            self::Cancelled => 'Annulé',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'fb-badge-ocre',
            self::Open => 'fb-badge-mousse',
            self::Closed => 'fb-badge-brique',
            self::Cancelled => 'fb-badge-gris',
        };
    }
}
