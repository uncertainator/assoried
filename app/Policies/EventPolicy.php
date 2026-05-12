<?php

namespace App\Policies;

use App\Models\Circle;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function create(User $user, Circle $circle): bool
    {
        return $circle->isManagedBy($user);
    }

    public function update(User $user, Event $event): bool
    {
        return $event->circle->isManagedBy($user);
    }

    public function delete(User $user, Event $event): bool
    {
        return $event->circle->isManagedBy($user);
    }
}
