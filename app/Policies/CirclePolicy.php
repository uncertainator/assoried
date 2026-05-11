<?php

namespace App\Policies;

use App\Models\Circle;
use App\Models\User;

class CirclePolicy
{
    public function update(User $user, Circle $circle): bool
    {
        return $circle->isManagedBy($user);
    }
}
