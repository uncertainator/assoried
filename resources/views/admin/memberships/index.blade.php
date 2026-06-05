<x-layouts.admin title="Demandes d'adhésion — Admin La Fabrique">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Administration</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Demandes d'adhésion
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
                <th>Date d'inscription</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr x-data="{ rejecting: false }">
                    <td>{{ $user->name ?: '—' }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:right;">
                        <div x-show="! rejecting" style="display:flex;gap:8px;justify-content:flex-end;">
                            <form method="POST" action="{{ route('admin.memberships.approve', $user) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="fb-btn fb-btn-primary fb-btn-sm">Valider</button>
                            </form>
                            <button type="button" class="fb-btn fb-btn-outline fb-btn-sm" @click="rejecting = true">Rejeter</button>
                        </div>

                        <form x-show="rejecting" x-cloak method="POST" action="{{ route('admin.memberships.reject', $user) }}"
                              style="display:flex;flex-direction:column;gap:8px;align-items:stretch;text-align:left;max-width:320px;margin-left:auto;">
                            @csrf
                            <textarea name="reason" rows="2" maxlength="1000" placeholder="Motif (optionnel)"
                                      class="fb-textarea" style="width:100%;"></textarea>
                            <div style="display:flex;gap:8px;justify-content:flex-end;">
                                <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm" @click="rejecting = false">Annuler</button>
                                <button type="submit" class="fb-btn fb-btn-outline fb-btn-sm">Confirmer le rejet</button>
                            </div>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;color:var(--fg-tertiary);padding:32px;">
                        Aucune demande d'adhésion en attente.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

</x-layouts.admin>
