<x-layouts.admin title="Modifier question — Parcours guidé">

<div style="max-width:700px;">
    <div style="margin-bottom:28px;">
        <a href="{{ route('admin.parcours.index') }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:underline;">← Retour au parcours</a>
        <div class="fb-eyebrow" style="margin-top:12px;">Parcours guidé</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Modifier la question
        </h1>
    </div>

    <form method="POST" action="{{ route('admin.parcours.questions.update', $question) }}">
        @csrf
        @method('PUT')
        @include('admin.parcours.questions._form')
        <div style="display:flex;gap:8px;margin-top:24px;">
            <button type="submit" class="fb-btn fb-btn-primary">Enregistrer</button>
            <a href="{{ route('admin.parcours.index') }}" class="fb-btn fb-btn-ghost">Annuler</a>
        </div>
    </form>

    @unless ($question->is_root)
        <div style="margin-top:24px;">
            <form method="POST" action="{{ route('admin.parcours.questions.set-root', $question) }}">
                @csrf
                <button type="submit" class="fb-btn fb-btn-ghost" style="font-size:13px;">
                    Définir comme question de départ
                </button>
            </form>
        </div>
    @endunless

    <div style="margin-top:40px;border-top:1px solid var(--border-subtle);padding-top:24px;">
        <form method="POST" action="{{ route('admin.parcours.questions.destroy', $question) }}"
              onsubmit="return confirm('Supprimer cette question et toutes ses options ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="fb-btn fb-btn-ghost" style="color:var(--brique-600);">
                Supprimer cette question
            </button>
        </form>
    </div>
</div>

</x-layouts.admin>
