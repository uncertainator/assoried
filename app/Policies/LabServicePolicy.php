<?php

namespace App\Policies;

use App\Models\LabService;
use App\Models\User;

class LabServicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LabService $labService): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, LabService $labService): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, LabService $labService): bool
    {
        return $this->canManage($user);
    }

    private function canManage(User $user): bool
    {
        return $user->isAdmin()
            || ($user->isReferent() && $user->assignedCircle?->slug === 'lab');
    }
}
