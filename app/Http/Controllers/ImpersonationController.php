<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ImpersonationController extends Controller
{
    /** Roles a superadmin may endorse — never superadmin itself. */
    private const IMPERSONABLE = [
        UserRole::Admin->value,
        UserRole::Referent->value,
        UserRole::Adherent->value,
    ];

    public function start(Request $request): RedirectResponse
    {
        Gate::authorize('impersonate');

        // Refuse to start a new impersonation while one is already active: the real
        // role is hidden mid-impersonation, so the user must stop first.
        if ($request->user()->isImpersonating()) {
            abort(409);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(self::IMPERSONABLE)],
        ]);

        $request->session()->put('impersonate_role', $validated['role']);

        AuditLog::record(AuditLog::TYPE_IMPERSONATION_START, $request->user(), [
            'new_role' => $validated['role'],
        ]);

        // Land on a route the simulated role can reach — back() would return to the
        // admin panel, which the endorsed (lower) role can no longer view (403).
        return redirect()->route('member.dashboard')
            ->with('success', 'Vous visualisez désormais en tant que '.UserRole::from($validated['role'])->label().'.');
    }

    public function stop(Request $request): RedirectResponse
    {
        // Allowed when an impersonation session exists AND the real DB role is
        // superadmin — independent of the (possibly simulated) effective role.
        $endorsed = $request->session()->get('impersonate_role');

        abort_if($endorsed === null, 404);
        abort_unless($request->user()->fresh()->role === UserRole::Superadmin, 403);

        AuditLog::record(AuditLog::TYPE_IMPERSONATION_STOP, $request->user(), [
            'new_role' => $endorsed,
        ]);

        $request->session()->forget('impersonate_role');

        return redirect()->route('admin.index')->with('success', 'Retour au rôle superadmin.');
    }
}
