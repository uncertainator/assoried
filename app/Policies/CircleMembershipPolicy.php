<?php

namespace App\Policies;

use App\Enums\MembershipStatus;
use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\User;

class CircleMembershipPolicy
{
    public function request(User $user, Circle $circle): bool
    {
        if ($user->isAdmin()) {
            return false;
        }

        if ($user->isReferent() && $user->assignedCircle?->id === $circle->id) {
            return false;
        }

        return true;
    }

    public function approve(User $user, CircleMembership $membership): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isReferent() && $user->assignedCircle?->id === $membership->circle_id;
    }

    public function reject(User $user, CircleMembership $membership): bool
    {
        return $this->approve($user, $membership);
    }

    public function cancel(User $user, CircleMembership $membership): bool
    {
        return $user->id === $membership->user_id
            && $membership->status === MembershipStatus::Pending;
    }

    public function exclude(User $user, CircleMembership $membership): bool
    {
        return $membership->circle->isManagedBy($user);
    }
}
