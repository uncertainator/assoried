<?php

namespace App\Enums;

enum LabRequestStatus: string
{
    case Nouvelle = 'nouvelle';
    case EnCours = 'en_cours';
    case Traitee = 'traitee';

    public function label(): string
    {
        return match ($this) {
            self::Nouvelle => 'Nouvelle',
            self::EnCours => 'En cours',
            self::Traitee => 'Traitée',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Nouvelle => 'fb-badge-ocre',
            self::EnCours => 'fb-badge-brique',
            self::Traitee => 'fb-badge-mousse',
        };
    }
}
