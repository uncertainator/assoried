<?php

namespace App\Policies;

use App\Enums\MembershipStatus;
use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\Poll;
use App\Models\User;

class PollPolicy
{
    public function create(User $user, ?Circle $circle): bool
    {
        if ($circle === null) {
            return $user->isAdmin();
        }

        return $circle->isManagedBy($user);
    }

    public function vote(User $user, Poll $poll): bool
    {
        if ($poll->isClosed()) {
            return false;
        }

        if ($poll->hasVoted($user)) {
            return false;
        }

        if ($poll->circle_id === null) {
            return true;
        }

        return CircleMembership::where('user_id', $user->id)
            ->where('circle_id', $poll->circle_id)
            ->where('status', MembershipStatus::Approved)
            ->exists();
    }
}
