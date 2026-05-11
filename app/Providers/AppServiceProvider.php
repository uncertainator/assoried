<?php

namespace App\Providers;

use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\Post;
use App\Models\User;
use App\Policies\CircleMembershipPolicy;
use App\Policies\CirclePolicy;
use App\Policies\PostPolicy;
use App\Policies\UserRolePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserRolePolicy::class);
        Gate::policy(Circle::class, CirclePolicy::class);
        Gate::policy(CircleMembership::class, CircleMembershipPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
    }
}
