<?php

namespace App\Policies;

use App\Models\ParcoursQuestion;
use App\Models\User;

class ParcoursQuestionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ParcoursQuestion $parcoursQuestion): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ParcoursQuestion $parcoursQuestion): bool
    {
        return $user->isAdmin();
    }
}
