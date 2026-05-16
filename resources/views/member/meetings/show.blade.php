<x-layouts.member :title="$meeting->title">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">{{ $meeting->title }}</h1>
        <div class="ea-greeting-sub">{{ $meeting->circle->name }}</div>
    </div>
    <div style="display:flex;gap:8px;">
        @can('create', [App\Models\MeetingReport::class, $meeting])
            <a href="{{ route('member.meeting-reports.create', $meeting) }}" class="fb-btn fb-btn-primary fb-btn-sm">
                + Nouveau compte-rendu
            </a>
        @endcan
        <a href="{{ route('member.circles.meetings.index', $meeting->circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
            ← Réunions du cercle
        </a>
    </div>
</div>

@if (session('success'))
    <div style="background:var(--success-bg, #f0fdf4);border:1px solid var(--success-border, #bbf7d0);border-radius:8px;padding:12px 16px;font-size:14px;color:var(--success-fg, #15803d);margin-bottom:20px;">
        {{ session('success') }}
    </div>
@endif

<div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:24px 28px;max-width:720px;display:flex;flex-direction:column;gap:20px;">

    {{-- Informations principales --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(180px, 1fr));gap:16px;">
        <div>
            <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:4px;">Date et heure</div>
            <div style="font-size:14px;color:var(--fg-primary);font-weight:500;">{{ $meeting->scheduled_at->translatedFormat('D d M Y à H\hi') }}</div>
        </div>

        @if ($meeting->duration_minutes)
            <div>
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:4px;">Durée estimée</div>
                <div style="font-size:14px;color:var(--fg-primary);">{{ $meeting->duration_minutes }} min</div>
            </div>
        @endif

        @if ($meeting->location)
            <div>
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:4px;">Lieu</div>
                <div style="font-size:14px;color:var(--fg-primary);">{{ $meeting->location }}</div>
            </div>
        @endif

        @if ($meeting->visio_url)
            <div>
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:4px;">Lien visio</div>
                <a href="{{ $meeting->visio_url }}" target="_blank" rel="noopener noreferrer" style="font-size:14px;color:var(--brique-500);word-break:break-all;">Rejoindre la réunion →</a>
            </div>
        @endif

        @if ($meeting->creator)
            <div>
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:4px;">Organisé par</div>
                <div style="font-size:14px;color:var(--fg-primary);">{{ $meeting->creator->name }}</div>
            </div>
        @endif
    </div>

    {{-- Ordre du jour --}}
    <div style="border-top:1px solid var(--border-subtle);padding-top:20px;">
        <h2 style="font-size:14px;font-weight:600;color:var(--fg-secondary);margin:0 0 14px;">Ordre du jour</h2>

        @if ($meeting->agendaItems->isEmpty())
            <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucun point défini.</p>
        @else
            <ol style="margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:8px;">
                @foreach ($meeting->agendaItems as $item)
                    <li style="display:flex;align-items:baseline;gap:12px;padding:10px 14px;background:var(--surface-subtle);border-radius:6px;">
                        <span style="font-size:12px;font-weight:700;color:var(--fg-tertiary);min-width:20px;">{{ $item->position }}</span>
                        <span style="font-size:14px;color:var(--fg-primary);flex:1;">{{ $item->title }}</span>
                        @if ($item->duration_minutes)
                            <span style="font-size:12px;color:var(--fg-tertiary);flex-shrink:0;">{{ $item->duration_minutes }} min</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

</div>

{{-- Comptes-rendus --}}
@php
    $reports = $meeting->reports()->get()->filter(fn ($r) => auth()->user()->can('view', $r));
@endphp

@if ($reports->isNotEmpty())
    <div style="max-width:720px;margin-top:24px;">
        <h2 style="font-size:14px;font-weight:600;color:var(--fg-secondary);margin:0 0 12px;">Comptes-rendus</h2>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach ($reports as $report)
                <a
                    href="{{ route('member.meeting-reports.show', $report) }}"
                    style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:8px;text-decoration:none;color:inherit;"
                >
                    <span style="font-size:14px;color:var(--fg-primary);">
                        Compte-rendu du {{ $report->created_at->translatedFormat('D d M Y') }}
                    </span>
                    @if ($report->isPublished())
                        <span style="font-size:12px;font-weight:600;padding:3px 9px;border-radius:20px;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;">Publié</span>
                    @else
                        <span style="font-size:12px;font-weight:600;padding:3px 9px;border-radius:20px;background:var(--surface-subtle);color:var(--fg-tertiary);border:1px solid var(--border-subtle);">Brouillon</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endif

</x-layouts.member>
