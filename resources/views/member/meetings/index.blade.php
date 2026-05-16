<x-layouts.member :title="'Réunions — '.$circle->name">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Réunions — {{ $circle->name }}</h1>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('member.circles.show', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">← Publications</a>
        @can('create', [App\Models\Meeting::class, $circle])
            <a href="{{ route('member.meetings.create', $circle) }}" class="fb-btn fb-btn-primary fb-btn-sm">+ Nouvelle réunion</a>
        @endcan
    </div>
</div>

@if ($meetings->isEmpty())
    <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucune réunion planifiée pour ce cercle.</p>
@else
    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach ($meetings as $meeting)
            <a href="{{ route('member.meetings.show', $meeting) }}" style="display:block;text-decoration:none;background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:8px;padding:16px 20px;transition:border-color .15s;" class="fb-card-link">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div>
                        <div style="font-size:15px;font-weight:600;color:var(--fg-primary);margin-bottom:2px;">{{ $meeting->title }}</div>
                        <div style="font-size:13px;color:var(--fg-tertiary);">
                            {{ $meeting->scheduled_at->translatedFormat('D d M Y à H\hi') }}
                            @if ($meeting->duration_minutes)
                                · {{ $meeting->duration_minutes }} min
                            @endif
                            @if ($meeting->location)
                                · {{ $meeting->location }}
                            @endif
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                        @if ($meeting->isPast())
                            <span style="font-size:11px;font-weight:500;color:var(--fg-tertiary);background:var(--surface-subtle);border-radius:4px;padding:2px 7px;">Passée</span>
                        @endif
                        <span style="font-size:12px;color:var(--fg-tertiary);">{{ $meeting->agendaItems->count() }} point{{ $meeting->agendaItems->count() > 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div style="margin-top:20px;">
        {{ $meetings->links() }}
    </div>
@endif

</x-layouts.member>
