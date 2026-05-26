<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'La Fabrique — Espace adhérent' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body>

<div class="ea-app">
    {{-- Sidebar --}}
    <aside class="ea-side">
        <div class="ea-side-brand">
            <a href="{{ route('home') }}" class="fb-logo">
                <img src="/images/logo-mark.svg" width="28" height="28" alt="">
           
            <span class="ea-side-brand-text">La <em>Fabrique</em></span>
            </a>
        </div>

        <div class="ea-nav-section">Espace</div>
        <a href="{{ route('member.dashboard') }}" class="ea-nav-item {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
            <svg class="ea-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12l9-9 9 9"/><path d="M5 10v10h14V10"/>
            </svg>
            Accueil
        </a>
        <a href="{{ route('member.circles.index') }}" class="ea-nav-item {{ request()->routeIs('member.circles.*') ? 'active' : '' }}">
            <svg class="ea-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/><path d="M8 12h8M12 8v8"/>
            </svg>
            Mes cercles
        </a>
        <div class="ea-nav-section">Créer</div>
        <a href="{{ route('member.feed') }}" class="ea-nav-item {{ request()->routeIs('member.feed') ? 'active' : '' }}">
            <svg class="ea-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 22V4h16v18"/><path d="M8 10h8M8 14h8M8 18h5"/>
            </svg>
            Publications
        </a>
        <a href="{{ route('member.agenda.index') }}" class="ea-nav-item {{ request()->routeIs('member.agenda.*') || request()->routeIs('member.circles.agenda') ? 'active' : '' }}">
            <svg class="ea-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/>
            </svg>
            Agenda
        </a>
        <a href="{{ route('member.polls.index') }}" class="ea-nav-item {{ request()->routeIs('member.polls.*') ? 'active' : '' }}">
            <svg class="ea-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18M3 12h12M3 18h8"/><circle cx="19" cy="18" r="3"/><path d="m21 20-1.5-1.5"/>
            </svg>
            Sondages
        </a>
        <a href="{{ route('member.scrutins.index') }}" class="ea-nav-item {{ request()->routeIs('member.scrutins.*') ? 'active' : '' }}">
            <svg class="ea-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 12l2 2 4-4"/><rect x="3" y="3" width="18" height="18" rx="2"/>
            </svg>
            Scrutins
        </a>
        <a href="{{ route('lab.services.index') }}" class="ea-nav-item {{ request()->routeIs('lab.services.*') ? 'active' : '' }}">
            <svg class="ea-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2M8 7V5a2 2 0 0 0-4 0v2"/>
            </svg>
            Catalogue Lab
        </a>

        

        @auth
        @if(Auth::user()->isAdmin())
        <a href="{{ route('admin.index') }}" class="ea-nav-item {{ request()->is('admin*') ? 'active' : '' }}">
            <svg class="ea-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a5 5 0 1 0 0 10A5 5 0 0 0 12 2z"/><path d="M12 14c-5 0-8 2.5-8 4v1h16v-1c0-1.5-3-4-8-4z"/><path d="M17 8l2 2 4-4"/>
            </svg>
            Administration
        </a>
        @endif
        @if(Auth::user()->isReferent())
        <a href="{{ route('referent.requests.index') }}" class="ea-nav-item {{ request()->routeIs('referent.requests.*') ? 'active' : '' }}" style="display:flex;align-items:center;">
            <svg class="ea-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 12h6M9 8h6M9 16h4"/><rect x="3" y="3" width="18" height="18" rx="2"/>
            </svg>
            Demandes @include('components._nav_badge')
        </a>
        <a href="{{ route('referent.circle.edit') }}" class="ea-nav-item {{ request()->routeIs('referent.circle.*') ? 'active' : '' }}">
            <svg class="ea-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/><path d="M12 2a4 4 0 0 1 0 8 4 4 0 0 1 0-8z"/>
            </svg>
            Mon cercle
        </a>
        @endif
        @endauth

        <div class="ea-nav-section">Compte</div>
        <a href="{{ route('member.profile') }}" class="ea-nav-item {{ request()->routeIs('member.profile') ? 'active' : '' }}">
            <svg class="ea-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="4"/><path d="M4 21c1-5 5-7 8-7s7 2 8 7"/>
            </svg>
            Mon profil
        </a>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="ea-nav-item" style="width:100%;background:none;border:none;text-align:left;">
                <svg class="ea-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Déconnexion
            </button>
        </form>

        <div class="ea-side-foot">
            <div class="ea-avatar">{{ strtoupper(substr(Auth::user()->name ?: Auth::user()->email, 0, 2)) }}</div>
            <div>
                <div class="ea-side-name-2">{{ Auth::user()->name ?: 'Adhérent' }}</div>
                <div class="ea-side-role-2">{{ Auth::user()->circles->count() }} cercle(s)</div>
            </div>
        </div>
    </aside>

    {{-- Main content --}}
    <main class="ea-main">
        @if (session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif
        {{ $slot }}
    </main>
</div>

<script src="https://unpkg.com/lucide@latest" defer></script>
<script>document.addEventListener('DOMContentLoaded', () => { if(window.lucide) lucide.createIcons(); });</script>
@stack('modals')
</body>
</html>
