<?php

namespace App\Http\Controllers;

use App\Enums\LabRequestStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreLabInternalRequestRequest;
use App\Models\Circle;
use App\Models\LabInternalRequest;
use App\Models\LabService;
use App\Models\User;
use App\Notifications\LabInternalRequestReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LabInternalRequestController extends Controller
{
    public function create(Request $request): View
    {
        $user = $request->user();

        $circles = Circle::where('is_active', true)->orderBy('name')->get();
        $services = LabService::latest()->get();
        $preselectedCircleId = $user->assignedCircle?->id ?? $request->query('circle_id');
        $preselectedServiceId = $request->query('service_id');

        return view('lab.requests.create', compact(
            'circles',
            'services',
            'preselectedCircleId',
            'preselectedServiceId',
        ));
    }

    public function store(StoreLabInternalRequestRequest $request): RedirectResponse
    {
        $labRequest = LabInternalRequest::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        $labRequest->load(['circle', 'labService', 'user']);

        $recipients = User::where('role', UserRole::Referent)
            ->whereHas('assignedCircle', fn ($q) => $q->where('slug', 'lab'))
            ->get()
            ->merge(User::where('role', UserRole::Admin)->get())
            ->unique('id');

        foreach ($recipients as $recipient) {
            $recipient->notify(new LabInternalRequestReceived($labRequest));
        }

        return redirect()->route('lab.requests.my')
            ->with('success', 'Votre demande a bien été envoyée au Lab.');
    }

    public function index(): View
    {
        Gate::authorize('viewAny', LabInternalRequest::class);

        $requests = LabInternalRequest::with(['circle', 'user', 'labService'])
            ->latest()
            ->get();

        $statuses = LabRequestStatus::cases();

        return view('lab.requests.index', compact('requests', 'statuses'));
    }

    public function updateStatus(Request $request, LabInternalRequest $labInternalRequest): RedirectResponse
    {
        Gate::authorize('updateStatus', $labInternalRequest);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', array_column(LabRequestStatus::cases(), 'value'))],
        ]);

        $labInternalRequest->update(['status' => $validated['status']]);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function myRequests(Request $request): View
    {
        $requests = LabInternalRequest::with(['circle', 'labService'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('lab.requests.my', compact('requests'));
    }
}
