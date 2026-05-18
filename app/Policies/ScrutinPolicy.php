<?php

namespace App\Policies;

use App\Models\Scrutin;
use App\Models\User;

class ScrutinPolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Scrutin $scrutin): bool
    {
        return $user->isAdmin() && $scrutin->isEditable();
    }

    public function publish(User $user, Scrutin $scrutin): bool
    {
        return $user->isAdmin() && $scrutin->isEditable();
    }

    public function close(User $user, Scrutin $scrutin): bool
    {
        return $user->isAdmin() && $scrutin->isOpen();
    }

    public function cancel(User $user, Scrutin $scrutin): bool
    {
        return $user->isAdmin() && $scrutin->canBeCancelled();
    }

    public function vote(User $user, Scrutin $scrutin): bool
    {
        return $scrutin->isVotable() && ! $scrutin->hasVoted($user);
    }

    public function viewResults(User $user, Scrutin $scrutin): bool
    {
        return $scrutin->isClosed();
    }
}
