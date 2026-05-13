<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabServiceRequest;
use App\Http\Requests\UpdateLabServiceRequest;
use App\Models\LabService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LabServiceController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', LabService::class);

        $services = LabService::latest()->get();

        return view('lab.index', compact('services'));
    }

    public function show(LabService $service): View
    {
        return view('lab.show', compact('service'));
    }

    public function create(): View
    {
        Gate::authorize('create', LabService::class);

        return view('lab.create');
    }

    public function store(StoreLabServiceRequest $request): RedirectResponse
    {
        LabService::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('lab.services.index')
            ->with('success', 'Service ajouté au catalogue.');
    }

    public function edit(LabService $service): View
    {
        Gate::authorize('update', $service);

        return view('lab.edit', compact('service'));
    }

    public function update(UpdateLabServiceRequest $request, LabService $service): RedirectResponse
    {
        $service->update($request->validated());

        return redirect()->route('lab.services.index')
            ->with('success', 'Service mis à jour.');
    }

    public function destroy(LabService $service): RedirectResponse
    {
        Gate::authorize('delete', $service);

        $service->delete();

        return redirect()->route('lab.services.index')
            ->with('success', 'Service supprimé.');
    }
}
