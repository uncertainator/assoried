<x-layouts.admin title="Logs d'audit — Superadmin">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Superadmin</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Logs d'audit
        </h1>
    </div>
</div>

<div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm);">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Auteur</th>
                <th>Cible</th>
                <th>Rôle</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td><span class="fb-badge">{{ $log->type }}</span></td>
                    <td>{{ $log->actor?->name ?: ($log->actor?->email ?? '—') }}</td>
                    <td>{{ $log->targetUser?->name ?: ($log->targetUser?->email ?? '—') }}</td>
                    <td>
                        @if ($log->old_role || $log->new_role)
                            {{ $log->old_role ?? '—' }} → {{ $log->new_role ?? '—' }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:var(--fg-tertiary);padding:32px;">
                        Aucune entrée d'audit.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($logs->hasPages())
    <div style="margin-top:20px;">
        {{ $logs->links() }}
    </div>
@endif

</x-layouts.admin>
