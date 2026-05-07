<?php

namespace App\Http\Controllers\Member;

use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
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

        return view('member.dashboard', compact('user', 'recentMemberships'));
    }
}
