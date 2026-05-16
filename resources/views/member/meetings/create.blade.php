<x-layouts.member :title="'Nouvelle réunion — '.$circle->name">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Nouvelle réunion</h1>
        <div class="ea-greeting-sub">{{ $circle->name }}</div>
    </div>
    <a href="{{ route('member.circles.meetings.index', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
        ← Réunions du cercle
    </a>
</div>

<div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:24px 28px;max-width:720px;">
    <form method="POST" action="{{ route('member.meetings.store', $circle) }}" style="display:flex;flex-direction:column;gap:16px;">
        @csrf
        @include('member.meetings._form')
        <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:8px;">
            <a href="{{ route('member.circles.meetings.index', $circle) }}" class="fb-btn fb-btn-ghost">Annuler</a>
            <button type="submit" class="fb-btn fb-btn-primary">Créer la réunion</button>
        </div>
    </form>
</div>

</x-layouts.member>
