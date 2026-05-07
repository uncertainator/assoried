<x-layouts.member title="Demandes d'inscription — La Fabrique">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Demandes d'inscription</h1>
        <div class="ea-greeting-sub">Cercle : <strong>{{ $circle->name }}</strong></div>
    </div>
</div>

@if ($memberships->isEmpty())
    <div class="ea-panel">
        <p style="font-size:14px;color:var(--fg-tertiary);margin:0;">Aucune demande en attente pour l'instant.</p>
    </div>
@else
    <div class="ea-panel">
        @foreach ($memberships as $membership)
            <div class="ea-event-row" style="align-items:flex-start;padding:16px 0;border-bottom:1px solid var(--border-subtle);">
                <div style="flex:1;">
                    <div class="ea-event-name">{{ $membership->user->name ?: $membership->user->email }}</div>
                    <div class="ea-event-meta-2" style="margin-top:4px;">
                        {{ $membership->user->email }}
                        · Demande envoyée le {{ $membership->joined_at->translatedFormat('d M Y à H:i') }}
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-shrink:0;">
                    <form method="POST" action="{{ route('referent.requests.approve', $membership) }}">
                        @csrf
                        <button type="submit" class="fb-btn fb-btn-primary fb-btn-sm"
                                onclick="return confirm('Approuver cette demande ?')">Approuver</button>
                    </form>
                    <form method="POST" action="{{ route('referent.requests.reject', $membership) }}"
                          x-data="{ open: false }" @submit.prevent="open = true">
                        @csrf
                        <div x-show="!open">
                            <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm" @click="open = true">Refuser</button>
                        </div>
                        <div x-show="open" x-cloak style="display:flex;gap:8px;align-items:center;">
                            <input type="text" name="reason" placeholder="Motif (optionnel)"
                                   style="font-size:13px;padding:6px 10px;border:1px solid var(--border-subtle);border-radius:6px;width:220px;" maxlength="500">
                            <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm" style="color:var(--brique-600);">Confirmer</button>
                            <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm" @click="open = false">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

</x-layouts.member>
