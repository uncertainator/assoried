<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\Meeting;
use App\Models\User;

class StatsController extends Controller
{
    public function index()
    {
        $totalMembers = User::count();
        $newMembers30Days = User::where('created_at', '>=', now()->subDays(30))->count();
        $membersByCircle = Circle::withCount('users')->orderBy('name')->get();

        $membershipCounts = CircleMembership::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingCount = $membershipCounts[MembershipStatus::Pending->value] ?? 0;
        $approvedCount = $membershipCounts[MembershipStatus::Approved->value] ?? 0;
        $rejectedCount = $membershipCounts[MembershipStatus::Rejected->value] ?? 0;

        $meetingsLast90Days = Meeting::where('scheduled_at', '>=', now()->subDays(90))->count();

        return view('admin.stats.index', compact(
            'totalMembers', 'newMembers30Days', 'membersByCircle',
            'pendingCount', 'approvedCount', 'rejectedCount',
            'meetingsLast90Days',
        ));
    }
}
