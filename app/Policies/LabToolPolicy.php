<?php

namespace App\Policies;

use App\Models\LabTool;
use App\Models\User;

class LabToolPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function download(User $user, LabTool $labTool): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, LabTool $labTool): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, LabTool $labTool): bool
    {
        return $this->canManage($user);
    }

    private function canManage(User $user): bool
    {
        return $user->isAdmin()
            || ($user->isReferent() && $user->assignedCircle?->slug === 'lab');
    }
}
