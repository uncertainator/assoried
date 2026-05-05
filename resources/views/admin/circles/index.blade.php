<x-layouts.admin title="Cercles — Admin La Fabrique">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Administration</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Cercles
        </h1>
    </div>
    <a href="{{ route('admin.circles.create') }}" class="fb-btn fb-btn-primary fb-btn-sm">+ Nouveau cercle</a>
</div>

<div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm);">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Slug</th>
                <th>Membres</th>
                <th>Max</th>
                <th>Statut</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($circles as $circle)
                <tr>
                    <td>
                        <div style="font-weight:600;color:var(--fg-primary);">{{ $circle->name }}</div>
                        <div style="font-size:12px;color:var(--fg-tertiary);">{{ Str::limit($circle->description, 60) }}</div>
                    </td>
                    <td><code style="font-family:var(--font-mono);font-size:12px;color:var(--fg-secondary);">{{ $circle->slug }}</code></td>
                    <td>{{ $circle->users_count }}</td>
                    <td>{{ $circle->max_members ?? '∞' }}</td>
                    <td>
                        @if ($circle->is_active)
                            <span class="fb-badge fb-badge-mousse">Actif</span>
                        @else
                            <span class="fb-badge fb-badge-brique">Inactif</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <a href="{{ route('admin.circles.edit', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Modifier</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:var(--fg-tertiary);padding:32px;">
                        Aucun cercle. <a href="{{ route('admin.circles.create') }}">En créer un</a>.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

</x-layouts.admin>
