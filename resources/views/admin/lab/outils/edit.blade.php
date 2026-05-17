<x-layouts.member title="Modifier l'outil — Admin Lab">

<div style="max-width:680px;">
    <div style="margin-bottom:24px;">
        <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:4px;">Admin · Lab</div>
        <h1 style="font-family:var(--font-display);font-size:1.75rem;font-weight:600;color:var(--fg-primary);margin:0;letter-spacing:-.02em;">Modifier l'outil</h1>
    </div>

    <div style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:28px;">
        <form action="{{ route('admin.lab.tools.update', $tool) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('admin.lab.outils._form')

            <div style="display:flex;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-subtle);">
                <button type="submit" class="fb-btn fb-btn-primary">Enregistrer</button>
                <a href="{{ route('admin.lab.tools.index') }}" class="fb-btn fb-btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

</x-layouts.member>
