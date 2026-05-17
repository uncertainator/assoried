<x-layouts.admin :title="'Modifier ' . $page->title . ' — Admin La Fabrique'">

<div style="max-width:720px;">
    <div style="margin-bottom:28px;">
        <a href="{{ route('admin.pages.index') }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:underline;">← Retour aux pages</a>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:8px 0 0;letter-spacing:-.02em;">
            Modifier : {{ $page->title }}
        </h1>
        <div style="font-size:12px;color:var(--fg-tertiary);margin-top:4px;font-family:var(--font-mono);">
            Slug : {{ $page->slug }} (non modifiable)
        </div>
    </div>

    <form method="POST" action="{{ route('admin.pages.update', $page) }}">
        @csrf @method('PUT')

        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--fg-secondary);margin-bottom:6px;">Titre</label>
            <input
                type="text"
                name="title"
                value="{{ old('title', $page->title) }}"
                maxlength="150"
                required
                style="width:100%;padding:10px 12px;border:1px solid var(--border-subtle);border-radius:var(--radius-md);font-size:14px;background:var(--bg-surface-2);color:var(--fg-primary);box-sizing:border-box;"
            >
            @error('title')
                <p style="color:var(--brique-600);font-size:12px;margin-top:4px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom:24px;">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--fg-secondary);margin-bottom:6px;">Contenu</label>
            <textarea
                name="content"
                rows="20"
                required
                style="width:100%;padding:10px 12px;border:1px solid var(--border-subtle);border-radius:var(--radius-md);font-size:14px;background:var(--bg-surface-2);color:var(--fg-primary);box-sizing:border-box;resize:vertical;font-family:var(--font-mono);line-height:1.6;"
            >{{ old('content', $page->content) }}</textarea>
            @error('content')
                <p style="color:var(--brique-600);font-size:12px;margin-top:4px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display:flex;gap:8px;">
            <button type="submit" class="fb-btn fb-btn-primary">Enregistrer</button>
            <a href="{{ route('admin.pages.index') }}" class="fb-btn fb-btn-ghost">Annuler</a>
        </div>
    </form>
</div>

</x-layouts.admin>
