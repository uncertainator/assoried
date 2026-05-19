<x-layouts.member :title="$event->title">

<div class="ea-topbar">
    <div>
        <a href="{{ route('member.circles.agenda', $event->circle) }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:none;">
            ← {{ $event->circle->name }}
        </a>
        <h1 class="ea-greeting" style="margin-top:4px;">{{ $event->title }}</h1>
        <div class="ea-greeting-sub">
            {{ $event->starts_at->translatedFormat('d F Y à H:i') }}
            @if ($event->location)
                · {{ $event->location }}
            @endif
        </div>
    </div>
    <div style="display:flex;gap:8px;flex-shrink:0;">
        @can('update', $event)
            <a href="{{ route('member.agenda.edit', $event) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Modifier</a>
        @endcan
    </div>
</div>

{{-- Infos de l'événement --}}
<div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:20px 24px;margin-bottom:24px;">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;">
        <div>
            <div style="font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:4px;">Date</div>
            <div style="font-size:14px;color:var(--fg-primary);">
                {{ $event->starts_at->translatedFormat('d F Y') }}<br>
                {{ $event->starts_at->format('H:i') }}
                @if ($event->ends_at) → {{ $event->ends_at->format('H:i') }} @endif
            </div>
        </div>
        @if ($event->location)
            <div>
                <div style="font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:4px;">Lieu</div>
                <div style="font-size:14px;color:var(--fg-primary);">{{ $event->location }}</div>
            </div>
        @endif
        @if ($event->tag)
            <div>
                <div style="font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:4px;">Type</div>
                <div style="font-size:14px;color:var(--fg-primary);">{{ $event->tag }}</div>
            </div>
        @endif
        <div>
            <div style="font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:4px;">Visibilité</div>
            <div>
                @if ($event->is_public)
                    <span class="fb-badge fb-badge-mousse">Public</span>
                @else
                    <span class="fb-badge" style="background:var(--bg-surface-3);color:var(--fg-secondary);">Privé</span>
                @endif
            </div>
        </div>
    </div>
    @if ($event->description)
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border-subtle);">
            <div style="font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:6px;">Description</div>
            <p style="font-size:14px;color:var(--fg-secondary);margin:0;line-height:1.6;">{{ $event->description }}</p>
        </div>
    @endif
</div>

{{-- Liste des inscrits --}}
<h2 style="font-size:15px;font-weight:600;margin:0 0 12px;color:var(--fg-secondary);">
    Inscrits
    <span style="font-weight:400;color:var(--fg-tertiary);">({{ $event->registrations->count() }})</span>
</h2>

@if ($event->registrations->isEmpty())
    <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucune inscription pour le moment.</p>
@else
    <div style="border:1px solid var(--border-subtle);border-radius:10px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:var(--bg-surface-3);border-bottom:1px solid var(--border-subtle);">
                    <th style="text-align:left;padding:10px 16px;font-size:12px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:var(--fg-tertiary);">Nom</th>
                    <th style="text-align:left;padding:10px 16px;font-size:12px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:var(--fg-tertiary);">Email</th>
                    <th style="text-align:left;padding:10px 16px;font-size:12px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:var(--fg-tertiary);">Inscrit le</th>
                    <th style="text-align:left;padding:10px 16px;font-size:12px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:var(--fg-tertiary);">Profil</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($event->registrations as $reg)
                    <tr style="border-bottom:1px solid var(--border-subtle);{{ $loop->last ? 'border-bottom:none;' : '' }}">
                        <td style="padding:12px 16px;font-size:14px;color:var(--fg-primary);">
                            {{ $reg->name ?: '—' }}
                        </td>
                        <td style="padding:12px 16px;font-size:14px;color:var(--fg-secondary);">
                            {{ $reg->email }}
                        </td>
                        <td style="padding:12px 16px;font-size:13px;color:var(--fg-tertiary);">
                            {{ $reg->created_at->translatedFormat('d M Y à H:i') }}
                        </td>
                        <td style="padding:12px 16px;">
                            @if ($reg->user_id)
                                <span class="fb-badge fb-badge-mousse" style="font-size:11px;">Membre</span>
                            @else
                                <span class="fb-badge" style="background:var(--bg-surface-3);color:var(--fg-tertiary);font-size:11px;">Visiteur</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

</x-layouts.member>
