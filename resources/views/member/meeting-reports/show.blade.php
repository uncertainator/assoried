<x-layouts.member :title="'Compte-rendu — '.$report->meeting->title">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Compte-rendu</h1>
        <div class="ea-greeting-sub">{{ $report->meeting->title }} · {{ $report->meeting->circle->name }}</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
        @if ($report->isDraft())
            <span style="font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;background:var(--surface-subtle);color:var(--fg-tertiary);border:1px solid var(--border-subtle);">
                Brouillon
            </span>
            @can('update', $report)
                <a href="{{ route('member.meeting-reports.edit', $report) }}" class="fb-btn fb-btn-primary fb-btn-sm">
                    Modifier
                </a>
            @endcan
        @else
            <span style="font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;">
                Publié
            </span>
        @endif
        <a href="{{ route('member.meetings.show', $report->meeting) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
            ← Retour à la réunion
        </a>
    </div>
</div>

@if (session('success'))
    <div style="background:var(--success-bg, #f0fdf4);border:1px solid var(--success-border, #bbf7d0);border-radius:8px;padding:12px 16px;font-size:14px;color:var(--success-fg, #15803d);margin-bottom:20px;">
        {{ session('success') }}
    </div>
@endif

<div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:24px 28px;max-width:720px;display:flex;flex-direction:column;gap:24px;">

    {{-- Méta --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(180px, 1fr));gap:16px;">
        <div>
            <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:4px;">Réunion du</div>
            <div style="font-size:14px;color:var(--fg-primary);font-weight:500;">{{ $report->meeting->scheduled_at->translatedFormat('D d M Y') }}</div>
        </div>
        @if ($report->creator)
            <div>
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:4px;">Rédigé par</div>
                <div style="font-size:14px;color:var(--fg-primary);">{{ $report->creator->name }}</div>
            </div>
        @endif
        @if ($report->published_at)
            <div>
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:4px;">Publié le</div>
                <div style="font-size:14px;color:var(--fg-primary);">{{ $report->published_at->translatedFormat('D d M Y') }}</div>
            </div>
        @endif
    </div>

    {{-- Participants --}}
    @if ($report->participants)
        <div style="border-top:1px solid var(--border-subtle);padding-top:20px;">
            <h2 style="font-size:14px;font-weight:600;color:var(--fg-secondary);margin:0 0 10px;">Participants présents</h2>
            <p style="font-size:14px;color:var(--fg-primary);line-height:1.6;margin:0;white-space:pre-wrap;">{{ $report->participants }}</p>
        </div>
    @endif

    {{-- Suivi de l'ordre du jour --}}
    @if ($report->meeting->agendaItems->isNotEmpty())
        <div style="border-top:1px solid var(--border-subtle);padding-top:20px;">
            <h2 style="font-size:14px;font-weight:600;color:var(--fg-secondary);margin:0 0 14px;">Suivi de l'ordre du jour</h2>
            <div style="display:flex;flex-direction:column;gap:14px;">
                @foreach ($report->meeting->agendaItems as $item)
                    <div style="padding:12px 16px;background:var(--surface-subtle);border-radius:8px;">
                        <div style="display:flex;gap:8px;align-items:baseline;margin-bottom:8px;">
                            <span style="font-size:12px;font-weight:700;color:var(--fg-tertiary);min-width:18px;">{{ $item->position }}.</span>
                            <span style="font-size:14px;font-weight:500;color:var(--fg-primary);">{{ $item->title }}</span>
                            @if ($item->duration_minutes)
                                <span style="font-size:12px;color:var(--fg-tertiary);">{{ $item->duration_minutes }} min</span>
                            @endif
                        </div>
                        @php $note = $report->agenda_notes[$item->id] ?? null; @endphp
                        @if ($note)
                            <p style="font-size:14px;color:var(--fg-secondary);line-height:1.6;margin:0 0 0 26px;white-space:pre-wrap;">{{ $note }}</p>
                        @else
                            <p style="font-size:13px;color:var(--fg-tertiary);font-style:italic;margin:0 0 0 26px;">Aucune note pour ce point.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Décisions --}}
    <div style="border-top:1px solid var(--border-subtle);padding-top:20px;">
        <h2 style="font-size:14px;font-weight:600;color:var(--fg-secondary);margin:0 0 10px;">Décisions prises</h2>
        @if (!empty($report->decisions))
            <ul style="margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:6px;">
                @foreach ($report->decisions as $decision)
                    <li style="font-size:14px;color:var(--fg-primary);padding:8px 14px;background:var(--surface-subtle);border-radius:6px;">{{ $decision }}</li>
                @endforeach
            </ul>
        @else
            <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;margin:0;">Aucune décision enregistrée.</p>
        @endif
    </div>

    {{-- Points ouverts --}}
    <div style="border-top:1px solid var(--border-subtle);padding-top:20px;">
        <h2 style="font-size:14px;font-weight:600;color:var(--fg-secondary);margin:0 0 10px;">Points ouverts / reportés</h2>
        @if (!empty($report->open_points))
            <ul style="margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:6px;">
                @foreach ($report->open_points as $point)
                    <li style="font-size:14px;color:var(--fg-primary);padding:8px 14px;background:var(--surface-subtle);border-radius:6px;">{{ $point }}</li>
                @endforeach
            </ul>
        @else
            <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;margin:0;">Aucun point ouvert.</p>
        @endif
    </div>

    {{-- Notes libres --}}
    @if ($report->free_notes)
        <div style="border-top:1px solid var(--border-subtle);padding-top:20px;">
            <h2 style="font-size:14px;font-weight:600;color:var(--fg-secondary);margin:0 0 10px;">Notes libres</h2>
            <p style="font-size:14px;color:var(--fg-primary);line-height:1.6;margin:0;white-space:pre-wrap;">{{ $report->free_notes }}</p>
        </div>
    @endif

</div>

</x-layouts.member>
