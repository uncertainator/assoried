<?php

namespace App\Policies;

use App\Enums\MembershipStatus;
use App\Models\Circle;
use App\Models\Meeting;
use App\Models\User;

class MeetingPolicy
{
    public function create(User $user, Circle $circle): bool
    {
        return $circle->isManagedBy($user);
    }

    public function viewAny(User $user, Circle $circle): bool
    {
        if ($circle->isManagedBy($user)) {
            return true;
        }

        return $circle->memberships()
            ->where('user_id', $user->id)
            ->where('status', MembershipStatus::Approved)
            ->exists();
    }

    public function view(User $user, Meeting $meeting): bool
    {
        return $this->viewAny($user, $meeting->circle);
    }
}
