<?php

namespace App\Http\Controllers\Member;

use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('circles');

        $recentMemberships = Auth::user()->memberships()
            ->with('circle')
            ->where(function ($q) {
                $q->where('status', MembershipStatus::Pending)
                    ->orWhere(function ($q2) {
                        $q2->whereIn('status', [MembershipStatus::Approved->value, MembershipStatus::Rejected->value])
                            ->where('validated_at', '>=', now()->subDays(30));
                    });
            })
            ->latest('joined_at')
            ->get();

        $circleIds = $user->circles->pluck('id');

        $feed = Post::with(['author', 'circle'])
            ->where(function ($q) use ($circleIds) {
                $q->where('pushed_to_general', true)
                    ->orWhereIn('circle_id', $circleIds);
            })
            ->latest()
            ->paginate(20);

        // 2 prochains événements par cercle — une seule requête, pas de N+1
        $upcomingEventsByCircle = $circleIds->isNotEmpty()
            ? Event::whereIn('circle_id', $circleIds)
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->get()
                ->groupBy('circle_id')
                ->map(fn ($events) => $events->take(2))
            : collect();

        return view('member.dashboard', compact('user', 'recentMemberships', 'feed', 'upcomingEventsByCircle'));
    }
}
