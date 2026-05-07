<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Referent = 'referent';
    case Adherent = 'adherent';
}
