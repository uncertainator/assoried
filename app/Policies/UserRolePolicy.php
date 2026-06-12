<?php

namespace App\Policies;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;

class UserRolePolicy
{
    public function promote(User $currentUser, User $targetUser): bool
    {
        return $this->canActOn($currentUser, $targetUser) && $currentUser->isAdmin();
    }

    public function demote(User $currentUser, User $targetUser): bool
    {
        return $this->canActOn($currentUser, $targetUser) && $currentUser->isAdmin();
    }

    /**
     * Full role management (incl. promotion to admin) — superadmin only.
     */
    public function changeRole(User $currentUser, User $targetUser): bool
    {
        // isSuperadmin() is the EFFECTIVE role: false while impersonating, so a
        // superadmin endorsing a lower role cannot mutate roles.
        return $this->canActOn($currentUser, $targetUser) && $currentUser->isSuperadmin();
    }

    /**
     * Exclude a member (governance act) — admin only, never on self, never
     * twice. canActOn() also shields superadmins and blocks impersonators.
     */
    public function exclude(User $currentUser, User $targetUser): bool
    {
        return $this->canActOn($currentUser, $targetUser)
            && $currentUser->isAdmin()
            && $currentUser->id !== $targetUser->id
            && $targetUser->account_status !== AccountStatus::Excluded;
    }

    /**
     * No inferior role may act on a superadmin target, and no role mutation is
     * allowed while impersonating. The superadmin is intouchable server-side.
     */
    private function canActOn(User $currentUser, User $targetUser): bool
    {
        if ($currentUser->isImpersonating()) {
            return false;
        }

        return $targetUser->role !== UserRole::Superadmin;
    }
}
