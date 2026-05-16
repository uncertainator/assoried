<?php

namespace App\Policies;

use App\Models\Circle;
use App\Models\CircleDocument;
use App\Models\User;

class CircleDocumentPolicy
{
    public function viewAny(User $user, Circle $circle): bool
    {
        return $circle->isManagedBy($user)
            || $circle->users()
                ->where('user_id', $user->id)
                ->wherePivot('status', 'approved')
                ->exists();
    }

    public function create(User $user, Circle $circle): bool
    {
        return $circle->isManagedBy($user);
    }

    public function delete(User $user, CircleDocument $document): bool
    {
        return $document->circle->isManagedBy($user);
    }
}
