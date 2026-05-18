<?php

namespace App\Models;

use App\Enums\ConsultationSource;
use Database\Factories\ConsultationReponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationReponse extends Model
{
    /** @use HasFactory<ConsultationReponseFactory> */
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'mode',
        'contenu',
        'ip_address',
        'source',
        'masque',
    ];

    protected function casts(): array
    {
        return [
            'source' => ConsultationSource::class,
            'masque' => 'boolean',
        ];
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function signatureArray(): ?array
    {
        return json_decode($this->contenu, true);
    }
}
