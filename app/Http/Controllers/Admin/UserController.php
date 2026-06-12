<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserPromoteRequest;
use App\Models\AuditLog;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('assignedCircle')->orderBy('name')->paginate(30);

        return view('admin.users.index', compact('users'));
    }

    public function promoteForm(User $user): View
    {
        Gate::authorize('promote', $user);
        abort_if(! $user->isAdherent(), 422, 'Seul un adhérent peut être promu référent.');

        $circles = Circle::whereNull('referent_id')->orderBy('name')->get();

        return view('admin.users.promote', compact('user', 'circles'));
    }

    public function promote(UserPromoteRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('promote', $user);

        DB::transaction(function () use ($request, $user) {
            $oldRole = $user->role;
            $circle = Circle::findOrFail($request->validated()['circle_id']);
            $user->role = UserRole::Referent;
            $user->save();
            $circle->referent_id = $user->id;
            $circle->save();

            AuditLog::record(AuditLog::TYPE_ROLE_CHANGE, $request->user(), [
                'target_user_id' => $user->id,
                'old_role' => $oldRole->value,
                'new_role' => UserRole::Referent->value,
            ]);
        });

        return redirect()->route('admin.users.index')
            ->with('success', $user->name.' a été promu·e référent·e.');
    }

    public function demote(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('demote', $user);
        abort_if(! $user->isReferent(), 422, 'Seul un référent peut être rétrogradé.');

        DB::transaction(function () use ($request, $user) {
            $oldRole = $user->role;
            Circle::where('referent_id', $user->id)->update(['referent_id' => null]);
            $user->role = UserRole::Adherent;
            $user->save();

            AuditLog::record(AuditLog::TYPE_ROLE_CHANGE, $request->user(), [
                'target_user_id' => $user->id,
                'old_role' => $oldRole->value,
                'new_role' => UserRole::Adherent->value,
            ]);
        });

        return redirect()->route('admin.users.index')
            ->with('success', $user->name.' a été rétrogradé·e en adhérent·e.');
    }

    /**
     * Full role management for a superadmin: set admin/referent/adherent.
     * Promotion to referent reuses the circle-assignment flow.
     */
    public function changeRole(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('changeRole', $user);

        $validated = $request->validate([
            'role' => ['required', Rule::in([
                UserRole::Admin->value,
                UserRole::Referent->value,
                UserRole::Adherent->value,
            ])],
        ]);

        $newRole = UserRole::from($validated['role']);

        if ($newRole === UserRole::Referent) {
            return redirect()->route('admin.users.promote.form', $user);
        }

        if ($user->role === $newRole) {
            return redirect()->route('admin.users.index')
                ->with('success', $user->name.' a déjà ce rôle.');
        }

        DB::transaction(function () use ($request, $user, $newRole) {
            $oldRole = $user->role;

            // Releasing a referent's circle when leaving the referent role.
            if ($user->role === UserRole::Referent) {
                Circle::where('referent_id', $user->id)->update(['referent_id' => null]);
            }

            $user->role = $newRole;
            $user->save();

            AuditLog::record(AuditLog::TYPE_ROLE_CHANGE, $request->user(), [
                'target_user_id' => $user->id,
                'old_role' => $oldRole->value,
                'new_role' => $newRole->value,
            ]);
        });

        return redirect()->route('admin.users.index')
            ->with('success', $user->name.' est désormais '.$newRole->label().'.');
    }
}
