<x-layouts.admin title="Demandes d'inscription — Admin">

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <div>
        <h1 style="font-size:22px;font-weight:600;color:var(--fg-primary);margin:0;">Demandes d'inscription</h1>
        <div style="font-size:14px;color:var(--fg-tertiary);margin-top:4px;">{{ $memberships->count() }} demande(s) en attente</div>
    </div>
</div>

{{-- Filtre par cercle --}}
<form method="GET" action="{{ route('admin.requests.index') }}" style="margin-bottom:20px;display:flex;gap:12px;align-items:center;">
    <select name="circle" onchange="this.form.submit()"
            style="font-size:14px;padding:8px 12px;border:1px solid var(--border-subtle);border-radius:6px;color:var(--fg-primary);background:#fff;">
        <option value="">Tous les cercles</option>
        @foreach ($circles as $c)
            <option value="{{ $c->slug }}" {{ request('circle') === $c->slug ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
    </select>
    @if (request('circle'))
        <a href="{{ route('admin.requests.index') }}" class="fb-btn fb-btn-ghost fb-btn-sm">Réinitialiser</a>
    @endif
</form>

@if ($memberships->isEmpty())
    <div class="ea-panel">
        <p style="font-size:14px;color:var(--fg-tertiary);margin:0;">Aucune demande en attente.</p>
    </div>
@else
    <div class="ea-panel" style="padding:0;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-subtle);">
                    <th style="text-align:left;padding:12px 16px;font-size:12px;font-weight:600;color:var(--fg-tertiary);text-transform:uppercase;letter-spacing:.06em;">Adhérent</th>
                    <th style="text-align:left;padding:12px 16px;font-size:12px;font-weight:600;color:var(--fg-tertiary);text-transform:uppercase;letter-spacing:.06em;">Cercle</th>
                    <th style="text-align:left;padding:12px 16px;font-size:12px;font-weight:600;color:var(--fg-tertiary);text-transform:uppercase;letter-spacing:.06em;">Date demande</th>
                    <th style="padding:12px 16px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($memberships as $membership)
                    <tr style="border-bottom:1px solid var(--border-subtle);">
                        <td style="padding:14px 16px;">
                            <div style="font-size:14px;font-weight:500;color:var(--fg-primary);">{{ $membership->user->name ?: '—' }}</div>
                            <div style="font-size:12px;color:var(--fg-tertiary);">{{ $membership->user->email }}</div>
                        </td>
                        <td style="padding:14px 16px;font-size:14px;color:var(--fg-secondary);">{{ $membership->circle->name }}</td>
                        <td style="padding:14px 16px;font-size:13px;color:var(--fg-tertiary);">{{ $membership->joined_at->translatedFormat('d M Y') }}</td>
                        <td style="padding:14px 16px;white-space:nowrap;">
                            <div style="display:flex;gap:8px;justify-content:flex-end;">
                                <form method="POST" action="{{ route('admin.requests.approve', $membership) }}">
                                    @csrf
                                    <button type="submit" class="fb-btn fb-btn-primary fb-btn-sm"
                                            onclick="return confirm('Approuver ?')">Approuver</button>
                                </form>
                                <form method="POST" action="{{ route('admin.requests.reject', $membership) }}"
                                      x-data="{ open: false }" @submit.prevent="open = true">
                                    @csrf
                                    <div x-show="!open">
                                        <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm" @click="open = true">Refuser</button>
                                    </div>
                                    <div x-show="open" x-cloak style="display:flex;gap:8px;align-items:center;">
                                        <input type="text" name="reason" placeholder="Motif"
                                               style="font-size:13px;padding:6px 10px;border:1px solid var(--border-subtle);border-radius:6px;width:200px;" maxlength="500">
                                        <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm" style="color:var(--brique-600);">Confirmer</button>
                                        <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm" @click="open = false">✕</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

</x-layouts.admin>
