<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Circle;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $upcoming = Event::upcoming()->with('circle')->get();
        $past = Event::with('circle')
            ->where('starts_at', '<', now())
            ->orderByDesc('starts_at')
            ->get();

        return view('member.agenda.index', compact('upcoming', 'past'));
    }

    public function circleIndex(Circle $circle): View
    {
        $upcoming = $circle->events()->upcoming()->get();
        $past = $circle->events()
            ->where('starts_at', '<', now())
            ->orderByDesc('starts_at')
            ->get();

        return view('member.agenda.circle', compact('circle', 'upcoming', 'past'));
    }

    public function create(Circle $circle): View
    {
        $this->authorize('create', [Event::class, $circle]);

        return view('member.agenda.create', compact('circle'));
    }

    public function store(StoreEventRequest $request, Circle $circle): RedirectResponse
    {
        $circle->events()->create([
            'author_id' => $request->user()->id,
            'title' => $request->validated('title'),
            'starts_at' => $request->validated('starts_at'),
            'ends_at' => $request->validated('ends_at'),
            'description' => $request->validated('description'),
            'location' => $request->validated('location'),
            'tag' => $request->validated('tag'),
            'foot_type' => $request->validated('foot_type'),
        ]);

        return redirect()->route('member.circles.agenda', $circle)
            ->with('success', 'Événement créé avec succès.');
    }

    public function show(Event $event): View
    {
        abort_unless(
            auth()->id() === $event->author_id || auth()->user()?->isAdmin() || auth()->user()?->isReferent(),
            403
        );

        $event->load(['circle', 'registrations']);

        return view('member.agenda.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        return view('member.agenda.edit', compact('event'));
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $event->update($request->validated());

        return redirect()->route('member.circles.agenda', $event->circle)
            ->with('success', 'Événement mis à jour.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $circle = $event->circle;
        $event->delete();

        return redirect()->route('member.circles.agenda', $circle)
            ->with('success', 'Événement supprimé.');
    }
}
