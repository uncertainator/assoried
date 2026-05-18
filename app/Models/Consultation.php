<?php

namespace App\Models;

use App\Enums\ConsultationMode;
use Database\Factories\ConsultationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultation extends Model
{
    /** @use HasFactory<ConsultationFactory> */
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'date_cloture',
        'mode_recueil',
        'options',
        'masque',
    ];

    protected function casts(): array
    {
        return [
            'date_cloture' => 'datetime',
            'mode_recueil' => ConsultationMode::class,
            'options' => 'array',
            'masque' => 'boolean',
        ];
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(ConsultationReponse::class);
    }

    public function estOuverte(): bool
    {
        return is_null($this->date_cloture) || $this->date_cloture->isFuture();
    }

    public function estCloturee(): bool
    {
        return ! $this->estOuverte();
    }

    /** @return array<string, int> */
    public function resultatsVote(): array
    {
        $options = $this->options ?? [];
        $counts = array_fill_keys($options, 0);

        $this->reponses()
            ->where('masque', false)
            ->select('contenu')
            ->get()
            ->each(function ($r) use (&$counts) {
                if (array_key_exists($r->contenu, $counts)) {
                    $counts[$r->contenu]++;
                }
            });

        return $counts;
    }

    public function resultatsSignatures(): int
    {
        return $this->reponses()->where('masque', false)->count();
    }
}
