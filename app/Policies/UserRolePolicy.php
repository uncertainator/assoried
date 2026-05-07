<?php

namespace App\Policies;

use App\Models\User;

class UserRolePolicy
{
    public function promote(User $currentUser, User $targetUser): bool
    {
        return $currentUser->isAdmin();
    }

    public function demote(User $currentUser, User $targetUser): bool
    {
        return $currentUser->isAdmin();
    }
}
