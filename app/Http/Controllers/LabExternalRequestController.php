<?php

namespace App\Http\Controllers;

use App\Enums\LabRequestStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreLabCitoyenRequest;
use App\Http\Requests\StoreLabEntrepriseRequest;
use App\Models\LabExternalRequest;
use App\Models\User;
use App\Notifications\LabExternalRequestConfirmation;
use App\Notifications\LabExternalRequestReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class LabExternalRequestController extends Controller
{
    public function showCitoyen(): View
    {
        return view('lab.external.citoyen');
    }

    public function storeCitoyen(StoreLabCitoyenRequest $request): RedirectResponse
    {
        $externalRequest = LabExternalRequest::create([
            ...$request->safe()->except('_pot'),
            'type' => 'citoyen',
        ]);

        $this->notifyRecipients($externalRequest);

        return redirect()->route('lab.external.confirmation')
            ->with('success', 'Votre demande a bien été transmise au Lab.');
    }

    public function showEntreprise(): View
    {
        return view('lab.external.entreprise');
    }

    public function storeEntreprise(StoreLabEntrepriseRequest $request): RedirectResponse
    {
        $externalRequest = LabExternalRequest::create([
            ...$request->safe()->except('_pot'),
            'type' => 'entreprise',
        ]);

        $this->notifyRecipients($externalRequest);

        return redirect()->route('lab.external.confirmation')
            ->with('success', 'Votre demande a bien été transmise au Lab.');
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', LabExternalRequest::class);

        $query = LabExternalRequest::latest();

        if ($request->filled('type') && in_array($request->type, ['citoyen', 'entreprise'])) {
            $query->where('type', $request->type);
        }

        $requests = $query->get();
        $statuses = LabRequestStatus::cases();

        return view('lab.external.index', compact('requests', 'statuses'));
    }

    public function updateStatus(Request $request, LabExternalRequest $labExternalRequest): RedirectResponse
    {
        Gate::authorize('updateStatus', $labExternalRequest);

        $validated = $request->validate([
            'statut' => ['required', 'string', 'in:'.implode(',', array_column(LabRequestStatus::cases(), 'value'))],
        ]);

        $labExternalRequest->update(['statut' => $validated['statut']]);

        return back()->with('success', 'Statut mis à jour.');
    }

    private function notifyRecipients(LabExternalRequest $externalRequest): void
    {
        $recipients = User::where('role', UserRole::Referent)
            ->whereHas('assignedCircle', fn ($q) => $q->where('slug', 'lab'))
            ->get()
            ->merge(User::where('role', UserRole::Admin)->get())
            ->unique('id');

        foreach ($recipients as $recipient) {
            $recipient->notify(new LabExternalRequestReceived($externalRequest));
        }

        Notification::route('mail', $externalRequest->email)
            ->notify(new LabExternalRequestConfirmation($externalRequest));
    }
}
