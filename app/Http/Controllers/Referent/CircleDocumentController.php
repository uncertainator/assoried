<?php

namespace App\Http\Controllers\Referent;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCircleDocumentRequest;
use App\Models\Circle;
use App\Models\CircleDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CircleDocumentController extends Controller
{
    public function create(Circle $circle): View
    {
        $this->authorize('create', [CircleDocument::class, $circle]);

        return view('referent.circle.documents.create', compact('circle'));
    }

    public function store(StoreCircleDocumentRequest $request, Circle $circle): RedirectResponse
    {
        $data = $request->validated();

        if ($data['type'] === 'pdf') {
            $file = $request->file('file');
            $uuid = (string) Str::uuid();
            $path = $file->storeAs(
                "circle-documents/{$circle->id}",
                $uuid.'.pdf',
                'public'
            );
            $data['file_path'] = $path;
            $data['original_filename'] = $file->getClientOriginalName();
            $data['url'] = null;
        } else {
            $data['file_path'] = null;
            $data['original_filename'] = null;
        }

        $data['circle_id'] = $circle->id;
        $data['uploaded_by'] = $request->user()->id;

        CircleDocument::create($data);

        return redirect()
            ->route('member.circles.documents.index', $circle)
            ->with('success', 'Document ajouté avec succès.');
    }

    public function destroy(Circle $circle, CircleDocument $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        if ($document->isPdf() && $document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('member.circles.documents.index', $circle)
            ->with('success', 'Document supprimé.');
    }
}
