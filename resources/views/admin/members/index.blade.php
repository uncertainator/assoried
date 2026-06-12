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
                <tr x-data="{ excluding: false }">
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->name ?: '—' }}</td>
                    <td>
                        @foreach ($member->circles as $circle)
                            <span class="fb-badge fb-badge-ocre" style="margin-right:4px;">{{ $circle->name }}</span>
                        @endforeach
                    </td>
                    <td>{{ $member->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:right;">
                        <div x-show="! excluding" style="display:flex;gap:8px;align-items:center;justify-content:flex-end;">
                            @if ($member->isAdmin())
                                <span class="fb-badge fb-badge-brique">Admin</span>
                            @endif
                            @if ($member->isExcluded())
                                <span class="fb-badge">Exclu·e</span>
                            @endif
                            @can('exclude', $member)
                                <button type="button" class="fb-btn fb-btn-outline fb-btn-sm" @click="excluding = true">Exclure</button>
                            @endcan
                        </div>

                        @can('exclude', $member)
                            <form x-show="excluding" x-cloak method="POST" action="{{ route('admin.members.exclude', $member) }}"
                                  style="display:flex;flex-direction:column;gap:8px;align-items:stretch;text-align:left;max-width:340px;margin-left:auto;">
                                @csrf
                                <p style="font-size:13px;color:var(--fg-tertiary);margin:0;">
                                    Exclusion irréversible : le compte sera désactivé et les données personnelles anonymisées.
                                </p>
                                <textarea name="reason" rows="2" maxlength="1000" placeholder="Motif (optionnel)"
                                          class="fb-textarea" style="width:100%;"></textarea>
                                <div style="display:flex;gap:8px;justify-content:flex-end;">
                                    <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm" @click="excluding = false">Annuler</button>
                                    <button type="submit" class="fb-btn fb-btn-outline fb-btn-sm">Confirmer l'exclusion</button>
                                </div>
                            </form>
                        @endcan
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
