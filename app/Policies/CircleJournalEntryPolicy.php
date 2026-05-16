<?php

namespace App\Policies;

use App\Enums\MembershipStatus;
use App\Models\Circle;
use App\Models\CircleJournalEntry;
use App\Models\User;

class CircleJournalEntryPolicy
{
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

    public function create(User $user, Circle $circle): bool
    {
        return $circle->isManagedBy($user);
    }

    public function update(User $user, CircleJournalEntry $entry): bool
    {
        return $user->isAdmin() || $entry->created_by === $user->id;
    }

    public function delete(User $user, CircleJournalEntry $entry): bool
    {
        return $user->isAdmin() || $entry->created_by === $user->id;
    }
}
