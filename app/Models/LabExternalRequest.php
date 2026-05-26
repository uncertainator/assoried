<?php

namespace App\Models;

use App\Enums\LabRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabExternalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'nom_contact',
        'email',
        'telephone',
        'raison_sociale',
        'territoire',
        'besoin_type',
        'type_projet',
        'fonction',
        'taille_organisation',
        'thematique',
        'message',
        'statut',
        'rgpd_consent',
    ];

    protected function casts(): array
    {
        return [
            'statut' => LabRequestStatus::class,
            'rgpd_consent' => 'boolean',
        ];
    }
}
