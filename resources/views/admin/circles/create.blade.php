<x-layouts.admin title="Nouveau cercle — Admin La Fabrique">

<div style="max-width:600px;">
    <div style="margin-bottom:28px;">
        <a href="{{ route('admin.circles.index') }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:underline;">← Retour aux cercles</a>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:8px 0 0;letter-spacing:-.02em;">
            Nouveau cercle
        </h1>
    </div>

    <form method="POST" action="{{ route('admin.circles.store') }}">
        @csrf
        @include('admin.circles._form')
        <div style="display:flex;gap:8px;margin-top:24px;">
            <button type="submit" class="fb-btn fb-btn-primary">Créer le cercle</button>
            <a href="{{ route('admin.circles.index') }}" class="fb-btn fb-btn-ghost">Annuler</a>
        </div>
    </form>
</div>

</x-layouts.admin>
