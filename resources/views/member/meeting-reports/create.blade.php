<x-layouts.member title="Nouveau compte-rendu">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Nouveau compte-rendu</h1>
        <div class="ea-greeting-sub">{{ $meeting->title }} · {{ $meeting->circle->name }}</div>
    </div>
    <a href="{{ route('member.meetings.show', $meeting) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
        ← Retour à la réunion
    </a>
</div>

<form
    action="{{ route('member.meeting-reports.store', $meeting) }}"
    method="POST"
    style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:24px 28px;max-width:720px;display:flex;flex-direction:column;gap:20px;"
>
    @csrf

    @php $report = null; @endphp

    @include('member.meeting-reports._form')

    <div style="display:flex;gap:12px;padding-top:4px;border-top:1px solid var(--border-subtle);">
        <button type="submit" class="fb-btn fb-btn-primary">
            Enregistrer en brouillon
        </button>
        <a href="{{ route('member.meetings.show', $meeting) }}" class="fb-btn fb-btn-ghost">
            Annuler
        </a>
    </div>
</form>

</x-layouts.member>
