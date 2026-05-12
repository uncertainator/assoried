<?php

namespace App\Policies;

use App\Models\Circle;
use App\Models\CircleAction;
use App\Models\User;

class CircleActionPolicy
{
    public function create(User $user, Circle $circle): bool
    {
        return $circle->isManagedBy($user);
    }

    public function update(User $user, CircleAction $action): bool
    {
        return $action->circle->isManagedBy($user);
    }

    public function delete(User $user, CircleAction $action): bool
    {
        return $action->circle->isManagedBy($user);
    }
}
