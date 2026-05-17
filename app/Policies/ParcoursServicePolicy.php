<?php

namespace App\Policies;

use App\Models\ParcoursService;
use App\Models\User;

class ParcoursServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ParcoursService $parcoursService): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ParcoursService $parcoursService): bool
    {
        return $user->isAdmin();
    }
}
