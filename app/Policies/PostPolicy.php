<?php

namespace App\Policies;

use App\Models\Circle;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function create(User $user, Circle $circle): bool
    {
        return $circle->isManagedBy($user);
    }

    public function pushToGeneral(User $user, Post $post): bool
    {
        return ! $post->pushed_to_general && $post->circle->isManagedBy($user);
    }

    public function delete(User $user, Post $post): bool
    {
        return $post->circle->isManagedBy($user);
    }
}
