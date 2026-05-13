<x-layouts.member :title="'Modifier — ' . $service->title">

<div style="max-width:640px;">
    <div style="margin-bottom:28px;">
        <a href="{{ route('lab.services.show', $service) }}" style="font-size:var(--text-sm);color:var(--fg-tertiary);text-decoration:underline;">← Retour au service</a>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:8px 0 0;letter-spacing:-.02em;">Modifier le service</h1>
    </div>

    <form method="POST" action="{{ route('lab.services.update', $service) }}">
        @csrf
        @method('PUT')
        @include('lab._form')
        <div style="display:flex;gap:8px;margin-top:24px;">
            <button type="submit" class="fb-btn fb-btn-primary">Enregistrer</button>
            <a href="{{ route('lab.services.show', $service) }}" class="fb-btn fb-btn-ghost">Annuler</a>
        </div>
    </form>
</div>

</x-layouts.member>
