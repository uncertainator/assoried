<x-layouts.member title="Nouveau service — Lab">

<div style="max-width:640px;">
    <div style="margin-bottom:28px;">
        <a href="{{ route('lab.services.index') }}" style="font-size:var(--text-sm);color:var(--fg-tertiary);text-decoration:underline;">← Retour au catalogue</a>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:8px 0 0;letter-spacing:-.02em;">Nouveau service</h1>
    </div>

    <form method="POST" action="{{ route('lab.services.store') }}">
        @csrf
        @include('lab._form')
        <div style="display:flex;gap:8px;margin-top:24px;">
            <button type="submit" class="fb-btn fb-btn-primary">Créer le service</button>
            <a href="{{ route('lab.services.index') }}" class="fb-btn fb-btn-ghost">Annuler</a>
        </div>
    </form>
</div>

</x-layouts.member>
