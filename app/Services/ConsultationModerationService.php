<?php

namespace App\Services;

use App\Models\ConsultationReponse;

class ConsultationModerationService
{
    public function masquerReponse(ConsultationReponse $reponse): ConsultationReponse
    {
        $reponse->update(['masque' => true]);

        return $reponse->fresh();
    }

    public function demasquerReponse(ConsultationReponse $reponse): ConsultationReponse
    {
        $reponse->update(['masque' => false]);

        return $reponse->fresh();
    }
}
