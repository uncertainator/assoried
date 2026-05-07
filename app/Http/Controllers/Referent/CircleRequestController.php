<?php

namespace App\Http\Controllers\Referent;

use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Referent\RejectCircleRequest;
use App\Models\CircleMembership;
use App\Notifications\CircleJoinDecisionNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CircleRequestController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $circle = Auth::user()->assignedCircle()->first();

        if (! $circle) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Vous n\'avez pas de cercle assigné.');
        }

        $memberships = CircleMembership::where('circle_id', $circle->id)
            ->where('status', MembershipStatus::Pending)
            ->with('user')
            ->latest('joined_at')
            ->get();

        return view('referent.requests.index', compact('memberships', 'circle'));
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
