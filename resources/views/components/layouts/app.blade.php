<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'La Fabrique' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<header class="fb-header">
    <a href="{{ route('home') }}" class="fb-logo">
        <img src="/images/logo-mark.svg" alt="" width="40" height="40">
        <span class="fb-logo-text">La Fabrique</span>
    </a>
    <nav class="fb-nav-links">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a>
        <a href="{{ route('evenements') }}" class="{{ request()->routeIs('evenements') ? 'active' : '' }}">Événements</a>
        @auth
            <a href="{{ route('member.dashboard') }}" class="{{ request()->routeIs('member.*') ? 'active' : '' }}">Mon espace</a>
        @else
            <a href="{{ route('inscription') }}" class="{{ request()->routeIs('inscription') ? 'active' : '' }}">Adhérer</a>
        @endauth
    </nav>
    @auth
        <a href="{{ route('member.dashboard') }}" class="fb-btn fb-btn-primary fb-btn-sm">Mon espace →</a>
    @else
        <a href="{{ route('login') }}" class="fb-btn fb-btn-primary fb-btn-sm">Espace adhérent →</a>
    @endauth
</header>

@if (session('success'))
    <div style="max-width:1280px;margin:16px auto;padding:0 48px;">
        <div class="flash-success">{{ session('success') }}</div>
    </div>
@endif
@if (session('error'))
    <div style="max-width:1280px;margin:16px auto;padding:0 48px;">
        <div class="flash-error">{{ session('error') }}</div>
    </div>
@endif

{{ $slot }}

<footer class="fb-footer">
    <div class="fb-footer-pattern" aria-hidden="true"></div>
    <div class="fb-footer-inner">
        <div>
            <div class="fb-footer-logo">La Fabrique</div>
            <p class="fb-footer-baseline">Association citoyenne · Alsace</p>
        </div>
        <div class="fb-footer-cols">
            <div>
                <div class="fb-footer-head">L'association</div>
                <a href="{{ route('home') }}">Qui sommes-nous</a>
                <a href="{{ route('inscription') }}">Adhérer</a>
                <a href="{{ route('legal.mentions') }}">Mentions légales</a>
            </div>
            <div>
                <div class="fb-footer-head">Activités</div>
                <a href="{{ route('evenements') }}">Événements</a>
                <a href="{{ route('inscription') }}">Cercles thématiques</a>
            </div>
            <div>
                <div class="fb-footer-head">Nous joindre</div>
                <a href="#">Alsace · Bas-Rhin</a>
                <a href="{{ route('legal.privacy') }}">Confidentialité</a>
            </div>
        </div>
    </div>
    <div class="fb-footer-bottom" style="margin-top:48px;padding-top:24px;">
        <span>© {{ date('Y') }} La Fabrique</span>
        <span class="fb-script">— faits ensemble.</span>
    </div>
</footer>

<script src="https://unpkg.com/lucide@latest" defer></script>
<script>document.addEventListener('DOMContentLoaded', () => { if(window.lucide) lucide.createIcons(); });</script>
</body>
</html>
