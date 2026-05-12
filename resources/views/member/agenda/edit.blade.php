<x-layouts.member :title="'Modifier — '.$event->title">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Modifier l'événement</h1>
        <div class="ea-greeting-sub">{{ $event->circle->name }}</div>
    </div>
    <a href="{{ route('member.circles.agenda', $event->circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
        ← Agenda du cercle
    </a>
</div>

<div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:24px 28px;max-width:680px;">
    <form method="POST" action="{{ route('member.agenda.update', $event) }}" style="display:flex;flex-direction:column;gap:16px;">
        @csrf
        @method('PUT')
        @include('member.agenda._form')
        <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:8px;">
            <a href="{{ route('member.circles.agenda', $event->circle) }}" class="fb-btn fb-btn-ghost">Annuler</a>
            <button type="submit" class="fb-btn fb-btn-primary">Enregistrer</button>
        </div>
    </form>
</div>

</x-layouts.member>
