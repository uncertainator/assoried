<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\CircleDocument;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CircleDocumentController extends Controller
{
    public function index(Circle $circle, Request $request): View
    {
        $this->authorize('viewAny', [CircleDocument::class, $circle]);

        $tag = $request->query('tag');

        $documents = CircleDocument::query()
            ->where('circle_id', $circle->id)
            ->when($tag, fn ($q) => $q->whereJsonContains('tags', $tag))
            ->orderBy('document_date', 'desc')
            ->get();

        $allTags = CircleDocument::query()
            ->where('circle_id', $circle->id)
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return view('member.circles.documents.index', compact('circle', 'documents', 'allTags', 'tag'));
    }
}
