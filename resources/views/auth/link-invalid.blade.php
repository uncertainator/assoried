<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lien invalide — La Fabrique</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--bg-page);">
<div style="max-width:420px;width:100%;padding:24px;text-align:center;">
    <img src="/images/logo-mark.svg" alt="La Fabrique" width="64" height="64" style="margin-bottom:24px;">
    <div class="fb-eyebrow" style="text-align:center;color:var(--danger);">Lien expiré ou invalide</div>
    <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:8px 0 16px;letter-spacing:-.02em;">
        Ce lien ne fonctionne plus
    </h1>
    <p style="font-family:var(--font-display);font-size:1.1rem;line-height:1.6;color:var(--fg-secondary);margin:0 0 32px;">
        Les liens de connexion sont valables 15 minutes et à usage unique.<br>
        On n'a pas reconnu celui-ci.
    </p>
    <a href="{{ route('login') }}" class="fb-btn fb-btn-primary fb-btn-lg" style="display:inline-flex;margin-bottom:16px;">
        Demander un nouveau lien →
    </a>
    <br>
    <a href="{{ route('home') }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:underline;">
        ← Retour à l'accueil
    </a>
</div>
</body>
</html>
