<?php

namespace App\Http\Controllers;

use App\Models\LabTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LabToolController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', LabTool::class);

        $tools = LabTool::where('active', true)
            ->orderBy('category')
            ->orderBy('title')
            ->get()
            ->groupBy(fn ($tool) => $tool->category ?? 'Autres');

        return view('lab.tools.index', compact('tools'));
    }

    public function download(Request $request, LabTool $tool): StreamedResponse
    {
        Gate::authorize('download', $tool);

        $tool->increment('downloads_count');

        return Storage::disk('local')->download(
            'lab-tools/'.$tool->file_path,
            $tool->title.'.pdf'
        );
    }
}
