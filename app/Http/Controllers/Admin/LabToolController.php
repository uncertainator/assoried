<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabToolRequest;
use App\Http\Requests\UpdateLabToolRequest;
use App\Models\LabTool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LabToolController extends Controller
{
    public function index(): View
    {
        Gate::authorize('create', LabTool::class);

        $tools = LabTool::orderBy('category')->orderBy('title')->get();

        return view('admin.lab.outils.index', compact('tools'));
    }

    public function create(): View
    {
        Gate::authorize('create', LabTool::class);

        return view('admin.lab.outils.create');
    }

    public function store(StoreLabToolRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $file = $request->file('file');
        $filename = Str::uuid().'.pdf';
        $file->storeAs('lab-tools', $filename, 'local');
        $data['file_path'] = $filename;
        $data['active'] = $request->boolean('active', true);
        $data['created_by'] = $request->user()->id;

        unset($data['file']);

        LabTool::create($data);

        return redirect()->route('admin.lab.tools.index')
            ->with('success', 'Outil ajouté à la bibliothèque.');
    }

    public function edit(LabTool $tool): View
    {
        Gate::authorize('update', $tool);

        return view('admin.lab.outils.edit', compact('tool'));
    }

    public function update(UpdateLabToolRequest $request, LabTool $tool): RedirectResponse
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        if ($request->hasFile('file')) {
            Storage::disk('local')->delete('lab-tools/'.$tool->file_path);
            $file = $request->file('file');
            $filename = Str::uuid().'.pdf';
            $file->storeAs('lab-tools', $filename, 'local');
            $data['file_path'] = $filename;
        }

        unset($data['file']);

        $tool->update($data);

        return redirect()->route('admin.lab.tools.index')
            ->with('success', 'Outil mis à jour.');
    }

    public function destroy(LabTool $tool): RedirectResponse
    {
        Gate::authorize('delete', $tool);

        Storage::disk('local')->delete('lab-tools/'.$tool->file_path);
        $tool->delete();

        return redirect()->route('admin.lab.tools.index')
            ->with('success', 'Outil supprimé.');
    }
}
