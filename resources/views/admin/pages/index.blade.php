<x-layouts.admin title="Pages statiques — Admin La Fabrique">

<div style="margin-bottom:28px;">
    <div class="fb-eyebrow">Administration</div>
    <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
        Pages statiques
    </h1>
</div>

@if (session('success'))
    <div style="background:var(--mousse-100);border:1px solid var(--mousse-300);color:var(--mousse-800);padding:12px 16px;border-radius:var(--radius-md);margin-bottom:20px;">
        {{ session('success') }}
    </div>
@endif

<div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm);">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Slug</th>
                <th>Dernière modification</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pages as $page)
                <tr>
                    <td style="font-weight:600;color:var(--fg-primary);">{{ $page->title }}</td>
                    <td><code style="font-family:var(--font-mono);font-size:12px;color:var(--fg-secondary);">{{ $page->slug }}</code></td>
                    <td style="color:var(--fg-tertiary);font-size:13px;">
                        {{ $page->updated_at->format('d/m/Y H:i') }}
                        @if ($page->updatedBy)
                            par {{ $page->updatedBy->name }}
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Modifier</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</x-layouts.admin>
