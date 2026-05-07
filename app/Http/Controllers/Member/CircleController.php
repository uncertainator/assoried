<?php

namespace App\Http\Controllers\Member;

use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\User;
use App\Notifications\CircleJoinRequestNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class CircleController extends Controller
{
    public function index()
    {
        $circles = Circle::where('is_active', true)->withCount('users')->get();
        $myMemberships = Auth::user()->memberships()->get()->keyBy('circle_id');

        return view('member.circles.index', compact('circles', 'myMemberships'));
    }

    public function join(Circle $circle): RedirectResponse
    {
        $user = Auth::user();

        if (! $circle->is_active) {
            return back()->with('error', 'Ce cercle n\'est pas disponible.');
        }

        if ($circle->isFull()) {
            return back()->with('error', 'Ce cercle est complet.');
        }

        $existing = CircleMembership::where('user_id', $user->id)
            ->where('circle_id', $circle->id)
            ->first();

        if ($existing?->status === MembershipStatus::Approved) {
            return back()->with('error', 'Vous êtes déjà membre de ce cercle.');
        }

        if ($existing?->status === MembershipStatus::Pending) {
            return back()->with('error', 'Votre demande est déjà en cours d\'examen.');
        }

        if ($existing?->status === MembershipStatus::Rejected) {
            $existing->delete();
        }

        $membership = CircleMembership::create([
            'user_id'   => $user->id,
            'circle_id' => $circle->id,
            'status'    => MembershipStatus::Pending,
            'joined_at' => now(),
        ]);

        $recipients = collect([$circle->referent])
            ->filter()
            ->merge(User::where('role', \App\Enums\UserRole::Admin)->get());

        try {
            Notification::send($recipients, new CircleJoinRequestNotification($membership));
        } catch (\Throwable $e) {
            logger()->error('CircleJoinRequestNotification failed: '.$e->getMessage());
        }

        return back()->with('success', 'Votre demande d\'inscription au cercle « '.$circle->name.' » a été envoyée.');
    }

    public function cancelRequest(Circle $circle): RedirectResponse
    {
        $membership = CircleMembership::where('user_id', Auth::id())
            ->where('circle_id', $circle->id)
            ->firstOrFail();

        $this->authorize('cancel', $membership);

        $membership->delete();

        return back()->with('success', 'Votre demande d\'inscription a été annulée.');
    }

    public function leave(Circle $circle): RedirectResponse
    {
        $deleted = CircleMembership::where('user_id', Auth::id())
            ->where('circle_id', $circle->id)
            ->where('status', MembershipStatus::Approved)
            ->delete();

        if (! $deleted) {
            return back()->with('error', 'Vous n\'êtes pas membre de ce cercle.');
        }

        return back()->with('success', 'Vous avez quitté le cercle « '.$circle->name.' ».');
    }
}
