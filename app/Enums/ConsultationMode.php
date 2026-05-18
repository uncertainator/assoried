<?php

namespace App\Enums;

enum ConsultationMode: string
{
    case AvisLibre = 'avis_libre';
    case Signature = 'signature';
    case VoteIndicatif = 'vote_indicatif';

    public function label(): string
    {
        return match ($this) {
            self::AvisLibre => 'Avis libre',
            self::Signature => 'Signature / pétition',
            self::VoteIndicatif => 'Vote indicatif',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::AvisLibre => 'fb-badge-ocre',
            self::Signature => 'fb-badge-mousse',
            self::VoteIndicatif => 'fb-badge-brique',
        };
    }
}
