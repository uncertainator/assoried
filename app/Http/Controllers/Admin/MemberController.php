<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ExcludeMember;
use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $circleFilter = $request->query('circle');
        $circles = Circle::orderBy('name')->get();

        $members = User::with('circles')
            ->when($circleFilter, fn ($q) => $q->whereHas('circles', fn ($q2) => $q2->where('slug', $circleFilter)))
            ->orderBy('email')
            ->paginate(30)
            ->withQueryString();

        return view('admin.members.index', compact('members', 'circles', 'circleFilter'));
    }

    public function exclude(Request $request, User $user, ExcludeMember $excludeMember): RedirectResponse
    {
        $this->authorize('exclude', $user);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $excludeMember->handle($user, $request->user(), $validated['reason'] ?? null);

        return redirect()->route('admin.members.index')
            ->with('success', 'Le membre a été exclu : compte désactivé et données anonymisées.');
    }

    public function export(Request $request): StreamedResponse
    {
        $circleFilter = $request->query('circle');

        $members = User::with('circles')
            ->when($circleFilter, fn ($q) => $q->whereHas('circles', fn ($q2) => $q2->where('slug', $circleFilter)))
            ->orderBy('email')
            ->get();

        $filename = 'membres-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($members) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM for Excel
            fputcsv($handle, ['Nom', 'Email', 'Cercles', 'Date inscription'], ';');

            foreach ($members as $user) {
                fputcsv($handle, [
                    $user->name ?: '—',
                    $user->email,
                    $user->circles->pluck('name')->join(', '),
                    $user->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
