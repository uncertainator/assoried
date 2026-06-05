<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MembershipApprovalService;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function __construct(private MembershipApprovalService $service) {}

    public function index()
    {
        $users = User::pending()->orderBy('created_at')->get();

        return view('admin.memberships.index', compact('users'));
    }

    public function approve(User $user)
    {
        $this->service->approve($user);

        return redirect()
            ->route('admin.memberships.index')
            ->with('success', 'Adhésion validée. Le candidat a été notifié par email.');
    }

    public function reject(Request $request, User $user)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->service->reject($user, $validated['reason'] ?? null);

        return redirect()
            ->route('admin.memberships.index')
            ->with('success', 'Demande rejetée. Le candidat a été notifié par email.');
    }
}
