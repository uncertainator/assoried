<?php

namespace App\Enums;

enum ParcoursCtaType: string
{
    case Contact = 'contact';
    case Inscription = 'inscription';
    case Demande = 'demande';

    public function label(): string
    {
        return match ($this) {
            self::Contact => 'Nous contacter',
            self::Inscription => 'S\'inscrire',
            self::Demande => 'Faire une demande',
        };
    }
}
