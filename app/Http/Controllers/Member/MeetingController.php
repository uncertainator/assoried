<?php

namespace App\Http\Controllers\Member;

use App\Actions\CreateMeetingAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMeetingRequest;
use App\Models\Circle;
use App\Models\Meeting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MeetingController extends Controller
{
    public function index(Circle $circle): View
    {
        $this->authorize('viewAny', [Meeting::class, $circle]);

        $meetings = $circle->meetings()
            ->with('agendaItems')
            ->orderByDesc('scheduled_at')
            ->paginate(20);

        return view('member.meetings.index', compact('circle', 'meetings'));
    }

    public function show(Meeting $meeting): View
    {
        $this->authorize('view', $meeting);

        $meeting->load('agendaItems', 'creator', 'circle');

        return view('member.meetings.show', compact('meeting'));
    }

    public function create(Circle $circle): View
    {
        $this->authorize('create', [Meeting::class, $circle]);

        return view('member.meetings.create', compact('circle'));
    }

    public function store(StoreMeetingRequest $request, Circle $circle, CreateMeetingAction $action): RedirectResponse
    {
        $meeting = $action->execute($circle, $request->user(), $request->validated());

        return redirect()->route('member.meetings.show', $meeting)
            ->with('success', 'Réunion créée avec succès.');
    }
}
