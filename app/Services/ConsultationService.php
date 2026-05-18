<?php

namespace App\Services;

use App\Enums\ConsultationMode;
use App\Enums\ConsultationSource;
use App\Models\Consultation;
use App\Models\ConsultationReponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConsultationService
{
    public function create(array $data): Consultation
    {
        return Consultation::create($data);
    }

    public function update(Consultation $consultation, array $data): Consultation
    {
        if ($consultation->estCloturee()) {
            throw ValidationException::withMessages([
                'consultation' => ['Cette consultation est clôturée et ne peut plus être modifiée.'],
            ]);
        }

        $consultation->update($data);

        return $consultation->fresh();
    }

    public function close(Consultation $consultation): Consultation
    {
        if ($consultation->estCloturee()) {
            throw ValidationException::withMessages([
                'consultation' => ['Cette consultation est déjà clôturée.'],
            ]);
        }

        $consultation->update(['date_cloture' => now()]);

        return $consultation->fresh();
    }

    public function checkAntiSpam(Consultation $consultation, string $ip): bool
    {
        return $consultation->reponses()
            ->where('ip_address', $ip)
            ->where('source', ConsultationSource::Numerique->value)
            ->where('created_at', '>=', now()->subHours(24))
            ->count() < 3;
    }

    public function soumettreReponse(Consultation $consultation, array $data, string $ip): ConsultationReponse
    {
        if (! $consultation->estOuverte()) {
            throw ValidationException::withMessages([
                'consultation' => ['Cette consultation est clôturée.'],
            ]);
        }

        if (! $this->checkAntiSpam($consultation, $ip)) {
            throw ValidationException::withMessages([
                'ip' => ['Vous avez atteint le nombre maximum de réponses autorisées (3 par 24h).'],
            ]);
        }

        $contenu = match ($consultation->mode_recueil) {
            ConsultationMode::AvisLibre => $data['contenu'],
            ConsultationMode::Signature => json_encode(['prenom' => $data['prenom'], 'nom' => $data['nom']]),
            ConsultationMode::VoteIndicatif => $data['choix'],
        };

        return DB::transaction(function () use ($consultation, $contenu, $ip) {
            return ConsultationReponse::create([
                'consultation_id' => $consultation->id,
                'mode' => $consultation->mode_recueil->value,
                'contenu' => $contenu,
                'ip_address' => $ip,
                'source' => ConsultationSource::Numerique->value,
                'masque' => false,
            ]);
        });
    }

    public function saisirReponsesTerrain(Consultation $consultation, array $reponses): void
    {
        DB::transaction(function () use ($consultation, $reponses) {
            foreach ($reponses as $reponse) {
                $contenu = match ($consultation->mode_recueil) {
                    ConsultationMode::AvisLibre => $reponse['contenu'],
                    ConsultationMode::Signature => json_encode(['prenom' => $reponse['prenom'], 'nom' => $reponse['nom']]),
                    ConsultationMode::VoteIndicatif => $reponse['choix'],
                };

                ConsultationReponse::create([
                    'consultation_id' => $consultation->id,
                    'mode' => $consultation->mode_recueil->value,
                    'contenu' => $contenu,
                    'ip_address' => null,
                    'source' => ConsultationSource::Terrain->value,
                    'masque' => false,
                ]);
            }
        });
    }
}
