<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — La Fabrique</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="ea-login-page">
    {{-- Left side — brand --}}
    <div class="ea-login-side">
        <a href="{{ route('home') }}" class="ea-side-logo">La <em>Fabrique</em></a>
        <div>
            <div class="ea-side-quote">« On se retrouve, on fait ensemble. C'est tout. »</div>
            <div class="ea-side-author">
                <span class="ea-side-name">— La Fabrique</span>
                <span class="ea-side-role">association citoyenne</span>
            </div>
        </div>
        <div style="font-size:12px;color:var(--brique-200);">Alsace · Bas-Rhin</div>
    </div>

    {{-- Right side — dual auth --}}
    <div class="ea-login-form">
        <div class="ea-login-form-inner" style="max-width:680px;">
            <div class="ea-login-eyebrow">Espace adhérent</div>
            <h1 class="ea-login-title" style="margin-bottom:8px;">Connexion</h1>
            <p class="ea-login-sub" style="margin-bottom:32px;">Choisissez votre méthode de connexion.</p>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:start;">

                {{-- Colonne gauche : mot de passe --}}
                <div>
                    <div style="font:600 13px var(--font-sans);color:var(--fg-primary);letter-spacing:.02em;margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--brique-500);">
                        Mot de passe
                    </div>

                    @if ($errors->hasBag('password'))
                        @foreach ($errors->getBag('password')->all() as $error)
                            <div class="flash-error" style="margin-bottom:12px;">{{ $error }}</div>
                        @endforeach
                    @endif

                    @if (session('info'))
                        <div class="flash-success" style="margin-bottom:12px;">{{ session('info') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.password') }}">
                        @csrf
                        <div class="ea-field">
                            <label for="login_email">Adresse email</label>
                            <input type="email" id="login_email" name="email"
                                   placeholder="votre@email.fr"
                                   value="{{ old('email') }}" required autocomplete="email">
                            @error('email', 'password')
                                <span class="ea-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="ea-field">
                            <label for="login_password" style="display:flex;justify-content:space-between;align-items:center;">
                                <span>Mot de passe</span>
                                <a href="{{ route('password.request') }}" style="font-size:12px;color:var(--brique-600);font-weight:400;">Mot de passe oublié ?</a>
                            </label>
                            <input type="password" id="login_password" name="password"
                                   autocomplete="current-password" required>
                            @error('password_field', 'password')
                                <span class="ea-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                            <input type="checkbox" id="remember" name="remember" value="1"
                                   style="accent-color:var(--brique-500);">
                            <label for="remember" style="font-size:13px;color:var(--fg-secondary);cursor:pointer;">Se souvenir de moi</label>
                        </div>
                        <button type="submit" class="ea-btn-primary" style="width:100%;">Se connecter →</button>
                    </form>
                </div>

                {{-- Colonne droite : magic link --}}
                <div>
                    <div style="font:600 13px var(--font-sans);color:var(--fg-primary);letter-spacing:.02em;margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--ocre-400,#c9a227);">
                        Lien magique
                    </div>

                    @if (session('error') && session('_login_method') === 'magic')
                        <div class="flash-error" style="margin-bottom:12px;">{{ session('error') }}</div>
                    @endif
                    @if (session('status') && session('_login_method') === 'magic')
                        <div class="flash-success" style="margin-bottom:12px;">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('auth.magic.send') }}">
                        @csrf
                        <div class="ea-field">
                            <label for="magic_email">Adresse email</label>
                            <input type="email" id="magic_email" name="email"
                                   placeholder="votre@email.fr"
                                   value="{{ old('email') }}" required autocomplete="email">
                        </div>
                        <p style="font-size:12px;color:var(--fg-tertiary);line-height:1.6;margin-bottom:16px;">
                            Vous recevrez un lien valable 15 minutes. Cliquez dessus pour vous connecter instantanément.
                        </p>
                        <button type="submit" class="ea-btn-primary" style="width:100%;background:var(--ocre-600,#a07a1a);border-color:var(--ocre-600,#a07a1a);">
                            Recevoir un lien →
                        </button>
                    </form>
                </div>

            </div>

            <div class="ea-signup-link" style="margin-top:28px;text-align:center;">
                Pas encore adhérent ?
                <a href="{{ route('inscription') }}">Rejoindre La Fabrique</a>
            </div>
            <div style="text-align:center;margin-top:10px;">
                <a href="{{ route('home') }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:underline;">
                    ← Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
