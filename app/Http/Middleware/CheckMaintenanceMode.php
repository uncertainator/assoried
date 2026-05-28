<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! (bool) Setting::get('maintenance_mode', false)) {
            return $next($request);
        }

        if ($request->routeIs('login', 'login.password', 'auth.magic.*', 'maintenance.bypass')) {
            return $next($request);
        }

        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        if (session('maintenance_bypass') === true) {
            return $next($request);
        }

        return response()->view('maintenance', [], 503);
    }
}
