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

    {{-- Right side — form --}}
    <div class="ea-login-form">
        <div class="ea-login-form-inner">
            <div class="ea-login-eyebrow">Espace adhérent</div>
            <h1 class="ea-login-title">Bonjour</h1>
            <p class="ea-login-sub">Entrez votre adresse email. On vous envoie un lien de connexion — pas de mot de passe à retenir.</p>

            @if (session('error'))
                <div class="flash-error" style="margin-bottom:16px;">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('auth.magic.send') }}">
                @csrf
                <div class="ea-field">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" placeholder="votre@email.fr"
                           value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                        <span class="ea-error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="ea-btn-primary">Recevoir mon lien →</button>
            </form>

            <div class="ea-signup-link" style="margin-top:24px;">
                Pas encore adhérent ?
                <a href="{{ route('inscription') }}">Rejoindre La Fabrique</a>
            </div>
            <div style="text-align:center;margin-top:12px;">
                <a href="{{ route('home') }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:underline;">
                    ← Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
