<?php

namespace App\Http\Controllers\Member;

use App\Enums\ScrutinStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\VoteScrutinRequest;
use App\Models\Scrutin;
use App\Models\ScrutinVote;
use App\Services\ScrutinService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScrutinController extends Controller
{
    public function __construct(private ScrutinService $service) {}

    public function index(): View
    {
        Scrutin::where('status', ScrutinStatus::Open)
            ->where('closes_at', '<', now())
            ->each(fn ($s) => $this->service->close($s, null));

        $open = Scrutin::where('status', ScrutinStatus::Open)
            ->orderBy('closes_at')
            ->get();

        $recentlyClosed = Scrutin::where('status', ScrutinStatus::Closed)
            ->where('closes_at', '>=', now()->subDays(30))
            ->orderByDesc('closes_at')
            ->get();

        return view('member.scrutins.index', compact('open', 'recentlyClosed'));
    }

    public function show(Scrutin $scrutin, Request $request): View
    {
        if ($scrutin->isExpired()) {
            $this->service->close($scrutin, null);
            $scrutin->refresh();
        }

        $user = $request->user();
        $hasVoted = $scrutin->hasVoted($user);
        $canVote = $user->can('vote', $scrutin);
        $scrutin->load('options');

        $voteCounts = [];
        if ($scrutin->isClosed()) {
            $voteCounts = $scrutin->votes()
                ->selectRaw('scrutin_option_id, count(*) as cnt')
                ->groupBy('scrutin_option_id')
                ->pluck('cnt', 'scrutin_option_id')
                ->map(fn ($v) => (int) $v)
                ->toArray();
        }

        return view('member.scrutins.show', compact('scrutin', 'hasVoted', 'canVote', 'voteCounts'));
    }

    public function vote(VoteScrutinRequest $request, Scrutin $scrutin): RedirectResponse
    {
        ScrutinVote::create([
            'scrutin_id' => $scrutin->id,
            'scrutin_option_id' => $request->validated('scrutin_option_id'),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('member.scrutins.show', $scrutin)
            ->with('success', 'Votre vote a été enregistré.');
    }
}
