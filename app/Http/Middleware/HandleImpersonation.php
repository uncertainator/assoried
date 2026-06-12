<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleImpersonation
{
    /**
     * Resolve the effective role from the session. The override is applied to a
     * non-persisted property only — never to the `role` attribute — so a later
     * save() on the authenticated user cannot leak the simulated role to the DB.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $role = $request->session()->get('impersonate_role');

        if ($user !== null && $role !== null) {
            // Guard on the REAL attribute, never the override: only a genuine
            // superadmin may have an active impersonation session honored.
            if ($user->role === UserRole::Superadmin) {
                $user->impersonatedRole = UserRole::from($role);
            } else {
                $request->session()->forget('impersonate_role');
            }
        }

        return $next($request);
    }
}
