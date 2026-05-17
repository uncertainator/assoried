<x-layouts.admin title="Statistiques — Admin La Fabrique">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Administration</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Statistiques
        </h1>
    </div>
    <a href="{{ route('admin.members.export') }}" class="fb-btn fb-btn-outline fb-btn-sm">
        Exporter membres CSV ↓
    </a>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">

    {{-- Membres --}}
    <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:24px;box-shadow:var(--shadow-sm);">
        <div class="fb-eyebrow" style="margin-bottom:16px;">Membres</div>
        <div style="display:flex;gap:32px;margin-bottom:20px;">
            <div>
                <div style="font-size:2.5rem;font-weight:700;color:var(--fg-primary);line-height:1;">{{ $totalMembers }}</div>
                <div style="font-size:12px;color:var(--fg-tertiary);margin-top:4px;">Total</div>
            </div>
            <div>
                <div style="font-size:2.5rem;font-weight:700;color:var(--brique-600);line-height:1;">{{ $newMembers30Days }}</div>
                <div style="font-size:12px;color:var(--fg-tertiary);margin-top:4px;">Nouveaux (30 j)</div>
            </div>
        </div>

        @if ($membersByCircle->isNotEmpty())
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left;color:var(--fg-tertiary);font-weight:500;padding-bottom:6px;border-bottom:1px solid var(--border-subtle);">Cercle</th>
                    <th style="text-align:right;color:var(--fg-tertiary);font-weight:500;padding-bottom:6px;border-bottom:1px solid var(--border-subtle);">Membres</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($membersByCircle as $circle)
                <tr>
                    <td style="padding:6px 0;color:var(--fg-primary);">{{ $circle->name }}</td>
                    <td style="padding:6px 0;text-align:right;font-weight:600;color:var(--fg-primary);">{{ $circle->users_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p style="font-size:13px;color:var(--fg-tertiary);margin:0;">Aucun cercle.</p>
        @endif
    </div>

    {{-- Inscriptions cercles --}}
    <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:24px;box-shadow:var(--shadow-sm);">
        <div class="fb-eyebrow" style="margin-bottom:16px;">Inscriptions cercles</div>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:14px;color:var(--fg-secondary);">En attente</span>
                <span style="font-size:1.5rem;font-weight:700;color:var(--ocre-600);">{{ $pendingCount }}</span>
            </div>
            <div style="border-top:1px solid var(--border-subtle);padding-top:12px;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:14px;color:var(--fg-secondary);">Approuvées</span>
                <span style="font-size:1.5rem;font-weight:700;color:var(--vert-600);">{{ $approvedCount }}</span>
            </div>
            <div style="border-top:1px solid var(--border-subtle);padding-top:12px;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:14px;color:var(--fg-secondary);">Refusées</span>
                <span style="font-size:1.5rem;font-weight:700;color:var(--fg-tertiary);">{{ $rejectedCount }}</span>
            </div>
        </div>
    </div>

    {{-- Réunions --}}
    <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:24px;box-shadow:var(--shadow-sm);">
        <div class="fb-eyebrow" style="margin-bottom:16px;">Réunions</div>
        <div style="font-size:2.5rem;font-weight:700;color:var(--fg-primary);line-height:1;">{{ $meetingsLast90Days }}</div>
        <div style="font-size:12px;color:var(--fg-tertiary);margin-top:4px;">Sur les 90 derniers jours</div>
    </div>

</div>

</x-layouts.admin>
