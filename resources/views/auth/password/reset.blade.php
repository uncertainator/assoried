<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Réinitialisation du mot de passe — La Fabrique</title>
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
            <div class="ea-login-eyebrow">Nouveau mot de passe</div>
            <h1 class="ea-login-title">Réinitialisation</h1>
            <p class="ea-login-sub">Choisissez un nouveau mot de passe pour votre compte.</p>

            @if ($errors->any())
                <div class="flash-error" style="margin-bottom:16px;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="ea-field">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email"
                           placeholder="votre@email.fr"
                           value="{{ old('email', request()->query('email')) }}"
                           required autocomplete="email">
                    @error('email')<span class="ea-error">{{ $message }}</span>@enderror
                </div>
                <div class="ea-field">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password"
                           autocomplete="new-password"
                           placeholder="8 caractères minimum" required>
                    @error('password')<span class="ea-error">{{ $message }}</span>@enderror
                </div>
                <div class="ea-field" style="margin-bottom:20px;">
                    <label for="password_confirmation">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           autocomplete="new-password" required>
                </div>
                <button type="submit" class="ea-btn-primary">Enregistrer le nouveau mot de passe →</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
