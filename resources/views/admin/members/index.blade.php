<x-layouts.admin title="Membres — Admin La Fabrique">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Administration</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Membres
        </h1>
    </div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <a href="{{ route('admin.members.export', request()->only('circle')) }}"
           class="fb-btn fb-btn-outline fb-btn-sm">
            Exporter CSV ↓
        </a>
    </div>
</div>

{{-- Filters --}}
<form method="GET" style="display:flex;gap:8px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
    <select name="circle" class="fb-select" style="width:auto;min-width:200px;" onchange="this.form.submit()">
        <option value="">Tous les cercles</option>
        @foreach ($circles as $circle)
            <option value="{{ $circle->slug }}" {{ $circleFilter === $circle->slug ? 'selected' : '' }}>
                {{ $circle->name }}
            </option>
        @endforeach
    </select>
    @if ($circleFilter)
        <a href="{{ route('admin.members.index') }}" class="fb-btn fb-btn-ghost fb-btn-sm">Effacer le filtre ×</a>
    @endif
    <span style="font-size:13px;color:var(--fg-tertiary);margin-left:8px;">
        {{ $members->total() }} membre(s)
    </span>
</form>

{{-- Table --}}
<div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm);">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Email</th>
                <th>Nom</th>
                <th>Cercles</th>
                <th>Inscrit le</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($members as $member)
                <tr>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->name ?: '—' }}</td>
                    <td>
                        @foreach ($member->circles as $circle)
                            <span class="fb-badge fb-badge-ocre" style="margin-right:4px;">{{ $circle->name }}</span>
                        @endforeach
                    </td>
                    <td>{{ $member->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:right;">
                        @if ($member->isAdmin())
                            <span class="fb-badge fb-badge-brique">Admin</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:var(--fg-tertiary);padding:32px;">
                        Aucun membre trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if ($members->hasPages())
    <div style="margin-top:20px;">
        {{ $members->links() }}
    </div>
@endif

</x-layouts.admin>
