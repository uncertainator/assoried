@php $isPast ??= false; $showCircle ??= false; @endphp
<div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:16px 20px;{{ $isPast ? 'opacity:0.55;' : '' }}">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div style="flex:1;min-width:180px;">
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                <span style="font-size:15px;font-weight:600;color:var(--fg-primary);">{{ $event->title }}</span>
                @if ($isPast)
                    <span class="fb-badge fb-badge-ocre">Passé</span>
                @endif
                @if ($showCircle)
                    <span class="fb-badge" style="background:var(--mousse-100);color:var(--mousse-700);">{{ $event->circle->name }}</span>
                @endif
            </div>
            <div style="font-size:13px;color:var(--fg-secondary);display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <span>
                    {{ $event->starts_at->translatedFormat('d F Y à H:i') }}
                    @if ($event->ends_at)
                        → {{ $event->ends_at->translatedFormat('H:i') }}
                    @endif
                </span>
                @if ($event->location)
                    <span style="color:var(--fg-tertiary);">📍 {{ $event->location }}</span>
                @endif
            </div>
            @if ($event->description)
                <div style="font-size:13px;color:var(--fg-tertiary);margin-top:6px;">{{ $event->description }}</div>
            @endif
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
            @can('update', $event)
                <a href="{{ route('member.agenda.show', $event) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Inscrits</a>
                <a href="{{ route('member.agenda.edit', $event) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Modifier</a>
            @endcan
            @can('delete', $event)
                <form method="POST" action="{{ route('member.agenda.destroy', $event) }}"
                      onsubmit="return confirm('Supprimer cet événement ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm" style="color:var(--brique-500);">Supprimer</button>
                </form>
            @endcan
        </div>
    </div>
</div>
