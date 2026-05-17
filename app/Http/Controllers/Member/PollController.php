<?php

namespace App\Http\Controllers\Member;

use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePollRequest;
use App\Http\Requests\VotePollRequest;
use App\Models\Circle;
use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PollController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $circleIds = $user->memberships()
            ->where('status', MembershipStatus::Approved)
            ->pluck('circle_id');

        $polls = Poll::where(function ($q) use ($circleIds) {
            $q->whereNull('circle_id')
                ->orWhereIn('circle_id', $circleIds);
        })
            ->orderByDesc('closes_at')
            ->get();

        return view('member.polls.index', compact('polls'));
    }

    public function create(): View
    {
        $this->authorize('create', [Poll::class, null]);

        return view('member.polls.create', ['circle' => null]);
    }

    public function store(StorePollRequest $request): RedirectResponse
    {
        $poll = Poll::create([
            'circle_id' => null,
            'created_by' => $request->user()->id,
            'title' => $request->validated('title'),
            'type' => $request->validated('type'),
            'options' => $request->validated('options'),
            'closes_at' => $request->validated('closes_at'),
        ]);

        return redirect()->route('member.polls.show', $poll)
            ->with('success', 'Sondage créé avec succès.');
    }

    public function createForCircle(Circle $circle): View
    {
        $this->authorize('create', [Poll::class, $circle]);

        return view('member.polls.create', compact('circle'));
    }

    public function storeForCircle(StorePollRequest $request, Circle $circle): RedirectResponse
    {
        $poll = Poll::create([
            'circle_id' => $circle->id,
            'created_by' => $request->user()->id,
            'title' => $request->validated('title'),
            'type' => $request->validated('type'),
            'options' => $request->validated('options'),
            'closes_at' => $request->validated('closes_at'),
        ]);

        return redirect()->route('member.polls.show', $poll)
            ->with('success', 'Sondage créé avec succès.');
    }

    public function show(Poll $poll, Request $request): View
    {
        $user = $request->user();
        $hasVoted = $poll->hasVoted($user);
        $results = $poll->isClosed() ? $poll->results() : null;
        $canVote = ! $poll->isClosed() && ! $hasVoted && $user->can('vote', $poll);

        return view('member.polls.show', compact('poll', 'hasVoted', 'results', 'canVote'));
    }

    public function vote(VotePollRequest $request, Poll $poll): RedirectResponse
    {
        PollVote::create([
            'poll_id' => $poll->id,
            'user_id' => $request->user()->id,
            'choice' => $request->validated('choice'),
        ]);

        return redirect()->route('member.polls.show', $poll)
            ->with('success', 'Votre vote a été enregistré.');
    }
}
