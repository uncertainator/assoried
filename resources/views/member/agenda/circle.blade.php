<x-layouts.member :title="'Agenda — '.$circle->name">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Agenda — {{ $circle->name }}</h1>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('member.circles.show', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">← Publications</a>
        @can('create', [App\Models\Event::class, $circle])
            <a href="{{ route('member.agenda.create', $circle) }}" class="fb-btn fb-btn-primary fb-btn-sm">+ Nouvel événement</a>
        @endcan
    </div>
</div>

{{-- Événements à venir --}}
<h2 style="font-size:15px;font-weight:600;margin:0 0 12px;color:var(--fg-secondary);">À venir</h2>

@if ($upcoming->isEmpty())
    <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;margin-bottom:32px;">Aucun événement à venir.</p>
@else
    <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:32px;">
        @foreach ($upcoming as $event)
            @include('member.agenda._event-row', ['event' => $event, 'showCircle' => false])
        @endforeach
    </div>
@endif

{{-- Événements passés --}}
@if ($past->isNotEmpty())
    <h2 style="font-size:15px;font-weight:600;margin:0 0 12px;color:var(--fg-tertiary);">Passés</h2>
    <div style="display:flex;flex-direction:column;gap:12px;">
        @foreach ($past as $event)
            @include('member.agenda._event-row', ['event' => $event, 'showCircle' => false, 'isPast' => true])
        @endforeach
    </div>
@endif

</x-layouts.member>
