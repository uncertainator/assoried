<x-layouts.member title="Mon profil — La Fabrique">

<div style="max-width:560px;">
    <div class="ea-topbar" style="margin-bottom:28px;">
        <div>
            <h1 class="ea-greeting">Mon <em>profil</em></h1>
        </div>
    </div>

    <div class="ea-panel" style="margin-bottom:20px;">
        <div class="ea-panel-head">
            <h2 class="ea-panel-title">Informations</h2>
        </div>
        <div style="font-size:14px;color:var(--fg-secondary);">
            <div style="margin-bottom:12px;display:flex;gap:8px;align-items:center;">
                <span style="font-weight:600;color:var(--fg-primary);width:80px;flex-shrink:0;">Email</span>
                <span>{{ Auth::user()->email }}</span>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <span style="font-weight:600;color:var(--fg-primary);width:80px;flex-shrink:0;">Nom</span>
                <span>{{ Auth::user()->name ?: '—' }}</span>
            </div>
        </div>
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border-subtle);">
            <p style="font-size:13px;color:var(--fg-tertiary);margin:0;">
                Pour modifier votre nom ou email, contactez-nous à
                <a href="#" style="color:var(--brique-600);">bonjour@lafabrique.fr</a>.
            </p>
        </div>
    </div>

    {{-- RGPD — delete account --}}
    <div x-data="{ open: false }" class="ea-panel" style="border-color:var(--brique-200);">
        <div class="ea-panel-head">
            <h2 class="ea-panel-title" style="color:var(--brique-700);">Zone de danger</h2>
        </div>
        <p style="font-size:14px;color:var(--fg-secondary);margin:0 0 16px;">
            La suppression de votre compte est définitive. Toutes vos données et inscriptions aux cercles seront effacées.
        </p>
        <button @click="open = true" class="fb-btn fb-btn-outline" style="border-color:var(--brique-400);color:var(--brique-700);">
            Supprimer mon compte
        </button>

        {{-- Confirmation modal --}}
        <div x-show="open" x-cloak
             style="position:fixed;inset:0;background:rgba(29,26,16,.5);display:flex;align-items:center;justify-content:center;z-index:50;">
            <div style="background:var(--bg-surface-2);border-radius:var(--radius-lg);padding:32px;max-width:400px;width:90%;box-shadow:var(--shadow-xl);">
                <h3 style="font-family:var(--font-display);font-size:1.4rem;color:var(--fg-primary);margin:0 0 12px;">
                    Confirmer la suppression ?
                </h3>
                <p style="font-size:14px;color:var(--fg-secondary);margin:0 0 24px;line-height:1.6;">
                    Cette action est irréversible. Votre compte et toutes vos données seront définitivement supprimés.
                </p>
                <div style="display:flex;gap:12px;">
                    <form method="POST" action="{{ route('member.account.destroy') }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="fb-btn fb-btn-primary" style="background:var(--brique-700);">
                            Oui, supprimer mon compte
                        </button>
                    </form>
                    <button @click="open = false" class="fb-btn fb-btn-ghost">Annuler</button>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top:20px;">
        <a href="{{ route('member.dashboard') }}" class="fb-btn fb-btn-outline fb-btn-sm">← Retour au tableau de bord</a>
    </div>
</div>

</x-layouts.member>
