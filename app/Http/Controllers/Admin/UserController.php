<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserPromoteRequest;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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
            $circle = Circle::findOrFail($request->validated()['circle_id']);
            $user->role = UserRole::Referent;
            $user->save();
            $circle->referent_id = $user->id;
            $circle->save();
        });

        return redirect()->route('admin.users.index')
            ->with('success', $user->name.' a été promu·e référent·e.');
    }

    public function demote(User $user): RedirectResponse
    {
        Gate::authorize('demote', $user);
        abort_if(! $user->isReferent(), 422, 'Seul un référent peut être rétrogradé.');

        DB::transaction(function () use ($user) {
            Circle::where('referent_id', $user->id)->update(['referent_id' => null]);
            $user->role = UserRole::Adherent;
            $user->save();
        });

        return redirect()->route('admin.users.index')
            ->with('success', $user->name.' a été rétrogradé·e en adhérent·e.');
    }
}
