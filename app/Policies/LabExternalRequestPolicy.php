<?php

namespace App\Policies;

use App\Models\LabExternalRequest;
use App\Models\User;

class LabExternalRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canManageLab($user);
    }

    public function updateStatus(User $user, LabExternalRequest $labExternalRequest): bool
    {
        return $this->canManageLab($user);
    }

    private function canManageLab(User $user): bool
    {
        return $user->isAdmin()
            || ($user->isReferent() && $user->assignedCircle?->slug === 'lab');
    }
}
