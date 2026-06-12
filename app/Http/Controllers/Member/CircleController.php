<?php

namespace App\Http\Controllers\Member;

use App\Enums\MembershipStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\User;
use App\Notifications\CircleJoinRequestNotification;
use App\Notifications\CircleLeaveNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class CircleController extends Controller
{
    public function index()
    {
        $myMemberships = Auth::user()->memberships()
            ->whereIn('status', [MembershipStatus::Approved, MembershipStatus::Pending])
            ->get()
            ->keyBy('circle_id');

        $circles = Circle::whereIn('id', $myMemberships->keys())
            ->withCount('users')
            ->get();

        return view('member.circles.index', compact('circles', 'myMemberships'));
    }

    public function discover()
    {
        $excludedIds = Auth::user()->memberships()
            ->whereIn('status', [MembershipStatus::Approved, MembershipStatus::Pending])
            ->pluck('circle_id');

        $circles = Circle::where('is_active', true)
            ->whereNotIn('id', $excludedIds)
            ->withCount('users')
            ->get();

        return view('member.circles.discover', compact('circles'));
    }

    public function directory(Circle $circle): View
    {
        $user = Auth::user();

        $this->authorize('viewDirectory', $circle);

        $members = $circle->users()
            ->wherePivot('status', MembershipStatus::Approved)
            ->orderBy('name')
            ->get();

        // Le référent peut ne pas avoir de ligne dans circle_user (assigné via referent_id)
        // mais doit quand même apparaître dans l'annuaire de son cercle.
        if ($circle->referent_id && ! $members->contains('id', $circle->referent_id) && $circle->referent) {
            $members = $members->push($circle->referent)->sortBy('name')->values();
        }

        $canSeeRoles = $circle->isManagedBy($user);

        return view('member.circles.directory', compact('circle', 'members', 'canSeeRoles'));
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
            'user_id' => $user->id,
            'circle_id' => $circle->id,
            'status' => MembershipStatus::Pending,
            'joined_at' => now(),
        ]);

        $recipients = collect([$circle->referent])
            ->filter()
            ->merge(User::where('role', UserRole::Admin)->get());

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
        $user = Auth::user();

        $membership = CircleMembership::where('user_id', $user->id)
            ->where('circle_id', $circle->id)
            ->where('status', MembershipStatus::Approved)
            ->first();

        if (! $membership) {
            return back()->with('error', 'Vous n\'êtes pas membre de ce cercle.');
        }

        $membership->delete();

        if ($circle->referent) {
            try {
                $circle->referent->notify(new CircleLeaveNotification($user, $circle));
            } catch (\Throwable $e) {
                logger()->error('CircleLeaveNotification failed: '.$e->getMessage());
            }
        }

        return redirect()->route('member.dashboard')
            ->with('success', 'Vous avez quitté le cercle « '.$circle->name.' ».');
    }
}
