<x-layouts.admin title="Consultations publiques — Admin">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Administration</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Consultations publiques
        </h1>
    </div>
    <a href="{{ route('admin.consultations.create') }}" class="fb-btn fb-btn-primary fb-btn-sm">
        + Nouvelle consultation
    </a>
</div>

@if (session('success'))
    <div style="background:var(--mousse-100);border:1px solid var(--mousse-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--mousse-700);">
        {{ session('success') }}
    </div>
@endif

@if ($consultations->isEmpty())
    <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucune consultation créée.</p>
@else
    <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-subtle);">
                    <th style="text-align:left;padding:12px 16px;font-weight:500;color:var(--fg-tertiary);">Titre</th>
                    <th style="text-align:left;padding:12px 16px;font-weight:500;color:var(--fg-tertiary);">Mode</th>
                    <th style="text-align:left;padding:12px 16px;font-weight:500;color:var(--fg-tertiary);">Statut</th>
                    <th style="text-align:left;padding:12px 16px;font-weight:500;color:var(--fg-tertiary);">Clôture</th>
                    <th style="text-align:right;padding:12px 16px;font-weight:500;color:var(--fg-tertiary);">Réponses</th>
                    <th style="text-align:right;padding:12px 16px;font-weight:500;color:var(--fg-tertiary);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($consultations as $consultation)
                    <tr style="border-bottom:1px solid var(--border-subtle);">
                        <td style="padding:12px 16px;">
                            <a href="{{ route('admin.consultations.show', $consultation) }}" style="font-weight:600;color:var(--fg-primary);text-decoration:none;">
                                {{ $consultation->titre }}
                            </a>
                            @if ($consultation->masque)
                                <span style="font-size:11px;color:var(--fg-tertiary);margin-left:6px;">(masquée)</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:var(--fg-secondary);">
                            {{ $consultation->mode_recueil->label() }}
                        </td>
                        <td style="padding:12px 16px;">
                            @if ($consultation->estOuverte())
                                <span style="background:var(--mousse-100);color:var(--mousse-700);font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;">Ouverte</span>
                            @else
                                <span style="background:var(--bg-surface-3);color:var(--fg-tertiary);font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;">Clôturée</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:var(--fg-secondary);">
                            {{ $consultation->date_cloture?->translatedFormat('j M Y') ?? '—' }}
                        </td>
                        <td style="padding:12px 16px;text-align:right;color:var(--fg-secondary);">
                            {{ $consultation->reponses_count }}
                        </td>
                        <td style="padding:12px 16px;text-align:right;">
                            <a href="{{ route('admin.consultations.show', $consultation) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Voir</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $consultations->links() }}</div>
@endif

</x-layouts.admin>
