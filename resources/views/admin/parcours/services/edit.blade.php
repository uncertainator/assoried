<x-layouts.admin title="Modifier {{ $service->name }} — Parcours guidé">

<div style="max-width:600px;">
    <div style="margin-bottom:28px;">
        <a href="{{ route('admin.parcours.index') }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:underline;">← Retour au parcours</a>
        <div class="fb-eyebrow" style="margin-top:12px;">Parcours guidé</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Modifier le service
        </h1>
    </div>

    <form method="POST" action="{{ route('admin.parcours.services.update', $service) }}">
        @csrf
        @method('PUT')
        @include('admin.parcours.services._form')
        <div style="display:flex;gap:8px;margin-top:24px;">
            <button type="submit" class="fb-btn fb-btn-primary">Enregistrer</button>
            <a href="{{ route('admin.parcours.index') }}" class="fb-btn fb-btn-ghost">Annuler</a>
        </div>
    </form>

    <div style="margin-top:40px;border-top:1px solid var(--border-subtle);padding-top:24px;">
        <form method="POST" action="{{ route('admin.parcours.services.destroy', $service) }}"
              onsubmit="return confirm('Supprimer ce service ? Les options qui y pointent seront déliées.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="fb-btn fb-btn-ghost" style="color:var(--brique-600);">
                Supprimer ce service
            </button>
        </form>
    </div>
</div>

</x-layouts.admin>
