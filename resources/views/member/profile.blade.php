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

    {{-- Sécurité — mot de passe --}}
    <div class="ea-panel" style="margin-bottom:20px;">
        <div class="ea-panel-head">
            <h2 class="ea-panel-title">Sécurité</h2>
        </div>

        @if (session('status'))
            <div class="flash-success" style="margin-bottom:16px;">{{ session('status') }}</div>
        @endif

        @if (is_null(Auth::user()->password))
            <p style="font-size:14px;color:var(--fg-secondary);margin:0 0 14px;line-height:1.6;">
                Votre compte utilise uniquement les liens magiques. Vous pouvez ajouter un mot de passe.
            </p>
            <a href="{{ route('account.password.setup') }}" class="fb-btn fb-btn-outline fb-btn-sm">
                Définir un mot de passe
            </a>
        @else
            <p style="font-size:14px;color:var(--fg-secondary);margin:0 0 16px;">
                Modifiez votre mot de passe de connexion.
            </p>

            @if ($errors->has('current_password') || $errors->has('new_password'))
                <div class="flash-error" style="margin-bottom:12px;">
                    @foreach ($errors->get('current_password') as $e)<div>{{ $e }}</div>@endforeach
                    @foreach ($errors->get('new_password') as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('member.password.update') }}">
                @csrf
                <div class="ea-field">
                    <label for="current_password">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password">
                    @error('current_password')<span class="ea-error">{{ $message }}</span>@enderror
                </div>
                <div class="ea-field">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" autocomplete="new-password" placeholder="8 caractères minimum">
                    @error('new_password')<span class="ea-error">{{ $message }}</span>@enderror
                </div>
                <div class="ea-field" style="margin-bottom:16px;">
                    <label for="new_password_confirmation">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" autocomplete="new-password">
                </div>
                <button type="submit" class="fb-btn fb-btn-primary fb-btn-sm">Modifier le mot de passe</button>
            </form>
        @endif
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
