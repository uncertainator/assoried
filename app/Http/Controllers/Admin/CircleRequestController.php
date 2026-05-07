<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Referent\RejectCircleRequest;
use App\Models\Circle;
use App\Models\CircleMembership;
use App\Notifications\CircleJoinDecisionNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CircleRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = CircleMembership::where('status', MembershipStatus::Pending)
            ->with(['user', 'circle']);

        if ($request->filled('circle')) {
            $query->whereHas('circle', fn ($q) => $q->where('slug', $request->circle));
        }

        $memberships = $query->latest('joined_at')->get();
        $circles = Circle::orderBy('name')->get();

        return view('admin.requests.index', compact('memberships', 'circles'));
    }

    public function approve(CircleMembership $membership): RedirectResponse
    {
        $this->authorize('approve', $membership);

        $membership->update([
            'status'       => MembershipStatus::Approved,
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        try {
            $membership->user->notify(new CircleJoinDecisionNotification($membership));
        } catch (\Throwable $e) {
            logger()->error('CircleJoinDecisionNotification failed: '.$e->getMessage());
        }

        return back()->with('success', 'La demande de '.$membership->user->name.' a été approuvée.');
    }

    public function reject(RejectCircleRequest $request, CircleMembership $membership): RedirectResponse
    {
        $this->authorize('reject', $membership);

        $membership->update([
            'status'           => MembershipStatus::Rejected,
            'validated_by'     => Auth::id(),
            'validated_at'     => now(),
            'rejection_reason' => $request->reason,
        ]);

        try {
            $membership->user->notify(new CircleJoinDecisionNotification($membership));
        } catch (\Throwable $e) {
            logger()->error('CircleJoinDecisionNotification failed: '.$e->getMessage());
        }

        return back()->with('success', 'La demande de '.$membership->user->name.' a été refusée.');
    }
}
