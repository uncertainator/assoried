<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Site en maintenance — La Fabrique</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;text-align:center;padding:2rem;">
    <div style="max-width:480px;">
        <div style="font-size:2.5rem;margin-bottom:1rem;">🔧</div>
        <h1 style="font-size:1.5rem;font-weight:700;margin-bottom:.75rem;">Site en maintenance</h1>
        <p style="color:#6b7280;margin-bottom:2rem;">
            Le site est temporairement indisponible pour maintenance. Merci de votre compréhension.
        </p>
        <a href="{{ route('login') }}" class="fb-btn fb-btn-primary">Se connecter</a>
    </div>
</div>
</body>
</html>
