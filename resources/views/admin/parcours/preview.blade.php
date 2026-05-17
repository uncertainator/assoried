<x-layouts.admin title="Prévisualisation de l'arbre — Parcours guidé">

<div style="margin-bottom:28px;">
    <a href="{{ route('admin.parcours.index') }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:underline;">← Retour au parcours</a>
    <div class="fb-eyebrow" style="margin-top:12px;">Parcours guidé</div>
    <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
        Prévisualisation de l'arbre
    </h1>
</div>

@if ($root === null)
    <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:32px;color:var(--fg-tertiary);">
        Aucune question racine définie. <a href="{{ route('admin.parcours.questions.create') }}" style="color:var(--brique-600);">Créer une question et la définir comme point de départ.</a>
    </div>
@else
    <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:28px;box-shadow:var(--shadow-sm);">
        @include('admin.parcours._tree-node', ['question' => $root, 'depth' => 0])
    </div>
@endif

</x-layouts.admin>
