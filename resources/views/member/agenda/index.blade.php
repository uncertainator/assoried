<x-layouts.member title="Agenda général">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Agenda général</h1>
        <div class="ea-greeting-sub">Tous les événements de tous les cercles</div>
    </div>
</div>

{{-- Événements à venir --}}
<h2 style="font-size:15px;font-weight:600;margin:0 0 12px;color:var(--fg-secondary);">À venir</h2>

@if ($upcoming->isEmpty())
    <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;margin-bottom:32px;">Aucun événement à venir.</p>
@else
    <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:32px;">
        @foreach ($upcoming as $event)
            @include('member.agenda._event-row', ['event' => $event, 'showCircle' => true])
        @endforeach
    </div>
@endif

{{-- Événements passés --}}
@if ($past->isNotEmpty())
    <h2 style="font-size:15px;font-weight:600;margin:0 0 12px;color:var(--fg-tertiary);">Passés</h2>
    <div style="display:flex;flex-direction:column;gap:12px;">
        @foreach ($past as $event)
            @include('member.agenda._event-row', ['event' => $event, 'showCircle' => true, 'isPast' => true])
        @endforeach
    </div>
@endif

</x-layouts.member>
