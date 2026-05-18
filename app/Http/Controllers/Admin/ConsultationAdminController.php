<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreConsultationRequest;
use App\Http\Requests\Admin\StoreTerrainReponseRequest;
use App\Http\Requests\Admin\UpdateConsultationRequest;
use App\Models\Consultation;
use App\Models\ConsultationReponse;
use App\Services\ConsultationModerationService;
use App\Services\ConsultationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConsultationAdminController extends Controller
{
    public function __construct(
        private ConsultationService $service,
        private ConsultationModerationService $moderationService,
    ) {}

    public function index(): View
    {
        $consultations = Consultation::withCount('reponses')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.consultations.index', compact('consultations'));
    }

    public function create(): View
    {
        return view('admin.consultations.create');
    }

    public function store(StoreConsultationRequest $request): RedirectResponse
    {
        $consultation = $this->service->create($request->validated());

        return redirect()->route('admin.consultations.show', $consultation)
            ->with('success', 'Consultation créée avec succès.');
    }

    public function show(Consultation $consultation): View
    {
        $reponses = $consultation->reponses()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.consultations.show', compact('consultation', 'reponses'));
    }

    public function edit(Consultation $consultation): View
    {
        return view('admin.consultations.edit', compact('consultation'));
    }

    public function update(UpdateConsultationRequest $request, Consultation $consultation): RedirectResponse
    {
        try {
            $this->service->update($consultation, $request->validated());
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('admin.consultations.show', $consultation)
            ->with('success', 'Consultation mise à jour.');
    }

    public function cloturer(Consultation $consultation): RedirectResponse
    {
        try {
            $this->service->close($consultation);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }

        return redirect()->route('admin.consultations.show', $consultation)
            ->with('success', 'Consultation clôturée.');
    }

    public function saisirTerrain(Consultation $consultation): View
    {
        return view('admin.consultations.saisie_terrain', compact('consultation'));
    }

    public function storeTerrain(StoreTerrainReponseRequest $request, Consultation $consultation): RedirectResponse
    {
        $this->service->saisirReponsesTerrain($consultation, $request->validated()['reponses']);

        return redirect()->route('admin.consultations.show', $consultation)
            ->with('success', 'Réponses terrain enregistrées.');
    }

    public function masquerReponse(ConsultationReponse $reponse): RedirectResponse
    {
        $this->moderationService->masquerReponse($reponse);

        return redirect()->back()->with('success', 'Réponse masquée.');
    }

    public function demasquerReponse(ConsultationReponse $reponse): RedirectResponse
    {
        $this->moderationService->demasquerReponse($reponse);

        return redirect()->back()->with('success', 'Réponse rendue visible.');
    }
}
