<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mot de passe oublié — La Fabrique</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="ea-login-page">
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

    <div class="ea-login-form">
        <div class="ea-login-form-inner">
            <div class="ea-login-eyebrow">Récupération</div>
            <h1 class="ea-login-title">Mot de passe oublié</h1>
            <p class="ea-login-sub">Entrez votre adresse email. Si un compte existe, vous recevrez un lien pour réinitialiser votre mot de passe.</p>

            @if (session('status'))
                <div class="flash-success" style="margin-bottom:16px;">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="flash-error" style="margin-bottom:16px;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="ea-field">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email"
                           placeholder="votre@email.fr"
                           value="{{ old('email') }}" required autocomplete="email">
                    @error('email')<span class="ea-error">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="ea-btn-primary">Envoyer le lien →</button>
            </form>

            <div style="text-align:center;margin-top:20px;">
                <a href="{{ route('login') }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:underline;">
                    ← Retour à la connexion
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
