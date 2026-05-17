<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreParcoursServiceRequest;
use App\Http\Requests\UpdateParcoursServiceRequest;
use App\Models\ParcoursService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ParcoursServiceController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', ParcoursService::class);

        $services = ParcoursService::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.parcours.services.index', compact('services'));
    }

    public function create(): View
    {
        Gate::authorize('create', ParcoursService::class);

        return view('admin.parcours.services.create');
    }

    public function store(StoreParcoursServiceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['created_by'] = $request->user()->id;

        ParcoursService::create($data);

        return redirect()->route('admin.parcours.index')
            ->with('success', 'Service créé avec succès.');
    }

    public function edit(ParcoursService $service): View
    {
        Gate::authorize('update', $service);

        return view('admin.parcours.services.edit', compact('service'));
    }

    public function update(UpdateParcoursServiceRequest $request, ParcoursService $service): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $service->update($data);

        return redirect()->route('admin.parcours.index')
            ->with('success', 'Service mis à jour.');
    }

    public function destroy(ParcoursService $service): RedirectResponse
    {
        Gate::authorize('delete', $service);

        $service->delete();

        return redirect()->route('admin.parcours.index')
            ->with('success', 'Service supprimé.');
    }
}
