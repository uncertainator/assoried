<x-layouts.admin title="Scrutins formels — Admin">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Administration</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Scrutins formels
        </h1>
    </div>
    <a href="{{ route('admin.scrutins.create') }}" class="fb-btn fb-btn-primary fb-btn-sm">
        + Nouveau scrutin
    </a>
</div>

@if (session('success'))
    <div style="background:var(--mousse-100);border:1px solid var(--mousse-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--mousse-700);">
        {{ session('success') }}
    </div>
@endif

@if ($scrutins->isEmpty())
    <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucun scrutin créé.</p>
@else
    <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-subtle);">
                    <th style="text-align:left;padding:12px 16px;font-weight:500;color:var(--fg-tertiary);">Titre</th>
                    <th style="text-align:left;padding:12px 16px;font-weight:500;color:var(--fg-tertiary);">Statut</th>
                    <th style="text-align:left;padding:12px 16px;font-weight:500;color:var(--fg-tertiary);">Ouverture</th>
                    <th style="text-align:left;padding:12px 16px;font-weight:500;color:var(--fg-tertiary);">Clôture</th>
                    <th style="text-align:right;padding:12px 16px;font-weight:500;color:var(--fg-tertiary);">Votes</th>
                    <th style="text-align:right;padding:12px 16px;font-weight:500;color:var(--fg-tertiary);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($scrutins as $scrutin)
                    <tr style="border-bottom:1px solid var(--border-subtle);">
                        <td style="padding:12px 16px;">
                            <a href="{{ route('admin.scrutins.show', $scrutin) }}" style="font-weight:600;color:var(--fg-primary);text-decoration:none;">
                                {{ $scrutin->title }}
                            </a>
                        </td>
                        <td style="padding:12px 16px;">
                            <span class="{{ $scrutin->status->badgeClass() }}" style="font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;white-space:nowrap;">
                                {{ $scrutin->status->label() }}
                            </span>
                            @if ($scrutin->isClosed() && $scrutin->result_status)
                                <span class="{{ $scrutin->result_status->badgeClass() }}" style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;white-space:nowrap;margin-left:4px;">
                                    {{ $scrutin->result_status->label() }}
                                </span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:var(--fg-secondary);">
                            {{ $scrutin->opened_at?->translatedFormat('j M Y') ?? '—' }}
                        </td>
                        <td style="padding:12px 16px;color:var(--fg-secondary);">
                            {{ $scrutin->closes_at?->translatedFormat('j M Y') ?? '—' }}
                        </td>
                        <td style="padding:12px 16px;text-align:right;color:var(--fg-secondary);">
                            {{ $scrutin->total_votes ?? $scrutin->votes()->count() }}
                        </td>
                        <td style="padding:12px 16px;text-align:right;">
                            <a href="{{ route('admin.scrutins.show', $scrutin) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Voir</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

</x-layouts.admin>
