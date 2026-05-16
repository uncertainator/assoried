<?php

namespace App\Policies;

use App\Enums\MembershipStatus;
use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\User;

class CirclePolicy
{
    public function update(User $user, Circle $circle): bool
    {
        return $circle->isManagedBy($user);
    }

    public function viewDirectory(User $user, Circle $circle): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($circle->referent_id === $user->id) {
            return true;
        }

        return CircleMembership::where('user_id', $user->id)
            ->where('circle_id', $circle->id)
            ->where('status', MembershipStatus::Approved)
            ->exists();
    }
}
