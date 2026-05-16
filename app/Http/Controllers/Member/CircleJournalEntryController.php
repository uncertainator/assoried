<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCircleJournalEntryRequest;
use App\Http\Requests\UpdateCircleJournalEntryRequest;
use App\Models\Circle;
use App\Models\CircleJournalEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CircleJournalEntryController extends Controller
{
    public function index(Circle $circle): View
    {
        $this->authorize('viewAny', [CircleJournalEntry::class, $circle]);

        $entries = $circle->journalEntries()
            ->with('author')
            ->orderByDesc('entry_date')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('member.circles.journal.index', compact('circle', 'entries'));
    }

    public function create(Circle $circle): View
    {
        $this->authorize('create', [CircleJournalEntry::class, $circle]);

        return view('member.circles.journal.create', compact('circle'));
    }

    public function store(StoreCircleJournalEntryRequest $request, Circle $circle): RedirectResponse
    {
        $circle->journalEntries()->create(
            array_merge($request->validated(), ['created_by' => $request->user()->id])
        );

        return redirect()->route('member.circles.journal.index', $circle)
            ->with('success', 'Entrée ajoutée au journal.');
    }

    public function edit(Circle $circle, CircleJournalEntry $entry): View
    {
        $this->authorize('update', $entry);

        return view('member.circles.journal.edit', compact('circle', 'entry'));
    }

    public function update(UpdateCircleJournalEntryRequest $request, Circle $circle, CircleJournalEntry $entry): RedirectResponse
    {
        $entry->update($request->validated());

        return redirect()->route('member.circles.journal.index', $circle)
            ->with('success', 'Entrée mise à jour.');
    }

    public function destroy(Circle $circle, CircleJournalEntry $entry): RedirectResponse
    {
        $this->authorize('delete', $entry);

        $entry->delete();

        return redirect()->route('member.circles.journal.index', $circle)
            ->with('success', 'Entrée supprimée.');
    }
}
