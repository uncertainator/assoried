<x-layouts.admin title="Nouvelle question — Parcours guidé">

<div style="max-width:700px;">
    <div style="margin-bottom:28px;">
        <a href="{{ route('admin.parcours.index') }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:underline;">← Retour au parcours</a>
        <div class="fb-eyebrow" style="margin-top:12px;">Parcours guidé</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Nouvelle question
        </h1>
    </div>

    <form method="POST" action="{{ route('admin.parcours.questions.store') }}">
        @csrf
        @include('admin.parcours.questions._form')
        <div style="display:flex;gap:8px;margin-top:24px;">
            <button type="submit" class="fb-btn fb-btn-primary">Créer la question</button>
            <a href="{{ route('admin.parcours.index') }}" class="fb-btn fb-btn-ghost">Annuler</a>
        </div>
    </form>
</div>

</x-layouts.admin>
