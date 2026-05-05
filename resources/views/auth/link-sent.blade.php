<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lien envoyé — La Fabrique</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--bg-page);">
<div style="max-width:420px;width:100%;padding:24px;text-align:center;">
    <img src="/images/logo-mark.svg" alt="La Fabrique" width="64" height="64" style="margin-bottom:24px;">
    <div class="fb-eyebrow" style="text-align:center;">Espace adhérent</div>
    <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:8px 0 16px;letter-spacing:-.02em;">
        Vérifiez votre boîte mail
    </h1>
    <p style="font-family:var(--font-display);font-size:1.1rem;line-height:1.6;color:var(--fg-secondary);margin:0 0 32px;">
        On vient d'envoyer un lien à <strong>{{ session('email') }}</strong>.<br>
        Il est valable 15 minutes.
    </p>
    <div style="background:var(--creme-100);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:20px;font-size:14px;color:var(--fg-tertiary);margin-bottom:24px;">
        Pas reçu ? Vérifiez vos spams, ou
        <a href="{{ route('login') }}" style="color:var(--brique-600);text-decoration:underline;">réessayez avec une autre adresse</a>.
    </div>
    <a href="{{ route('home') }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:underline;">
        ← Retour à l'accueil
    </a>
</div>
</body>
</html>
