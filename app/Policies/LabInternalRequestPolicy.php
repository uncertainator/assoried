<?php

namespace App\Policies;

use App\Models\LabInternalRequest;
use App\Models\User;

class LabInternalRequestPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function viewAny(User $user): bool
    {
        return $this->canManageLab($user);
    }

    public function updateStatus(User $user, LabInternalRequest $labInternalRequest): bool
    {
        return $this->canManageLab($user);
    }

    private function canManageLab(User $user): bool
    {
        return $user->isAdmin()
            || ($user->isReferent() && $user->assignedCircle?->slug === 'lab');
    }
}
