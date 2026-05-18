<?php

namespace App\Http\Controllers;

use App\Enums\ConsultationMode;
use App\Http\Requests\SoumettreConsultationRequest;
use App\Models\Consultation;
use App\Services\ConsultationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConsultationPublicController extends Controller
{
    public function __construct(private ConsultationService $service) {}

    public function show(Consultation $consultation): View
    {
        abort_if($consultation->masque, 404);

        return view('consultations.show', compact('consultation'));
    }

    public function soumettre(SoumettreConsultationRequest $request, Consultation $consultation): RedirectResponse
    {
        try {
            $this->service->soumettreReponse($consultation, $request->validated(), $request->ip());
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Votre réponse a bien été enregistrée. Merci de votre participation !');
    }

    public function resultats(Consultation $consultation): View
    {
        abort_if($consultation->masque, 404);

        $avisLibres = null;
        $resultats = null;

        if ($consultation->mode_recueil === ConsultationMode::AvisLibre) {
            $avisLibres = $consultation->reponses()
                ->where('masque', false)
                ->select(['id', 'contenu', 'created_at'])
                ->latest()
                ->paginate(20);
        } elseif ($consultation->mode_recueil === ConsultationMode::VoteIndicatif) {
            $resultats = $consultation->resultatsVote();
        } else {
            $resultats = $consultation->resultatsSignatures();
        }

        return view('consultations.resultats', compact('consultation', 'resultats', 'avisLibres'));
    }

    public function terrainPrint(Consultation $consultation): View
    {
        return view('consultations.print', compact('consultation'));
    }
}
