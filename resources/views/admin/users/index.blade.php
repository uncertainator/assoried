<x-layouts.admin title="Rôles & Référents — Admin La Fabrique">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Administration</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Rôles &amp; Référents
        </h1>
    </div>
</div>

{{-- Table --}}
<div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm);">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Cercle assigné</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->name ?: '—' }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if ($user->role === \App\Enums\UserRole::Superadmin)
                            <span class="fb-badge fb-badge-brique">Superadmin</span>
                        @elseif ($user->role === \App\Enums\UserRole::Admin)
                            <span class="fb-badge fb-badge-brique">Admin</span>
                        @elseif ($user->role === \App\Enums\UserRole::Referent)
                            <span class="fb-badge fb-badge-ocre">Référent</span>
                        @else
                            <span class="fb-badge">Adhérent</span>
                        @endif
                    </td>
                    <td>
                        @if ($user->role === \App\Enums\UserRole::Referent && $user->assignedCircle)
                            {{ $user->assignedCircle->name }}
                        @else
                            —
                        @endif
                    </td>
                    <td style="text-align:right;">
                        @if ($user->role === \App\Enums\UserRole::Superadmin)
                            {{-- Superadmin is intouchable from the UI. --}}
                            <span style="color:var(--fg-tertiary);font-size:13px;">—</span>
                        @elseif (auth()->user()->isSuperadmin())
                            {{-- Full role management — superadmin only. Never offers superadmin. --}}
                            <form method="POST" action="{{ route('admin.users.role', $user) }}" style="display:inline-flex;gap:6px;align-items:center;"
                                  onsubmit="return confirm('Changer le rôle de {{ addslashes($user->name) }} ?')">
                                @csrf
                                <select name="role" class="fb-select fb-select-sm" style="width:auto;">
                                    <option value="admin" {{ $user->role === \App\Enums\UserRole::Admin ? 'selected' : '' }}>Admin</option>
                                    <option value="referent" {{ $user->role === \App\Enums\UserRole::Referent ? 'selected' : '' }}>Référent</option>
                                    <option value="adherent" {{ $user->role === \App\Enums\UserRole::Adherent ? 'selected' : '' }}>Adhérent</option>
                                </select>
                                <button type="submit" class="fb-btn fb-btn-outline fb-btn-sm">Appliquer</button>
                            </form>
                        @elseif ($user->isAdherent())
                            <a href="{{ route('admin.users.promote.form', $user) }}"
                               class="fb-btn fb-btn-outline fb-btn-sm">
                                Promouvoir
                            </a>
                        @elseif ($user->isReferent())
                            <form method="POST" action="{{ route('admin.users.demote', $user) }}" style="display:inline;"
                                  onsubmit="return confirm('Rétrograder {{ addslashes($user->name) }} en adhérent·e ?')">
                                @csrf
                                <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm">
                                    Rétrograder
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:var(--fg-tertiary);padding:32px;">
                        Aucun utilisateur trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if ($users->hasPages())
    <div style="margin-top:20px;">
        {{ $users->links() }}
    </div>
@endif

</x-layouts.admin>
