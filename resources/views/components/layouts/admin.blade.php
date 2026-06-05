<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'La Fabrique — Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-brand">
            <div class="admin-sidebar-brand-text">La <em>Fabrique</em> <span style="font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:var(--brique-300);display:block;margin-top:2px;">Admin</span></div>
        </div>

        <div class="admin-nav-head">Gestion</div>
        <a href="{{ route('admin.members.index') }}" class="admin-nav-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.87"/></svg>
            Membres
        </a>
        <a href="{{ route('admin.circles.index') }}" class="admin-nav-link {{ request()->routeIs('admin.circles.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8 12h8M12 8v8"/></svg>
            Cercles
        </a>
        <a href="{{ route('admin.requests.index') }}" class="admin-nav-link {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}" style="display:flex;align-items:center;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6M9 8h6M9 16h4"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
            Demandes @include('components._nav_badge')
        </a>
        <a href="{{ route('admin.memberships.index') }}" class="admin-nav-link {{ request()->routeIs('admin.memberships.*') ? 'active' : '' }}" style="display:flex;align-items:center;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
            Adhésions
            @php $pendingMembers = \App\Models\User::pending()->count(); @endphp
            @if ($pendingMembers > 0)
                <span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 5px;background:#c85226;color:#fff;font-size:11px;font-weight:600;border-radius:9px;margin-left:6px;line-height:1;">{{ $pendingMembers }}</span>
            @endif
        </a>
        <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a5 5 0 1 0 0 10A5 5 0 0 0 12 2z"/><path d="M12 14c-5 0-8 2.5-8 4v1h16v-1c0-1.5-3-4-8-4z"/><path d="M17 8l2 2 4-4"/></svg>
            Rôles &amp; Référents
        </a>
        <a href="{{ route('admin.stats') }}" class="admin-nav-link {{ request()->routeIs('admin.stats*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            Statistiques
        </a>
        <a href="{{ route('admin.parcours.index') }}" class="admin-nav-link {{ request()->routeIs('admin.parcours.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h4l3 8 4-16 3 8h4"/></svg>
            Parcours guidé
        </a>
        <a href="{{ route('admin.scrutins.index') }}" class="admin-nav-link {{ request()->routeIs('admin.scrutins.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
            Scrutins
        </a>
        <a href="{{ route('admin.consultations.index') }}" class="admin-nav-link {{ request()->routeIs('admin.consultations.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Consultations
        </a>

        @php $maintenanceActive = (bool) \App\Models\Setting::get('maintenance_mode', false); @endphp
        <div class="admin-nav-head">Système</div>
        <form method="POST" action="{{ route('admin.maintenance.toggle') }}" style="margin:0;">
            @csrf
            <button type="submit" class="admin-nav-link" style="width:100%;background:none;border:none;text-align:left;cursor:pointer;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                Maintenance
                @if($maintenanceActive)
                    <span style="font-size:10px;background:#dc2626;color:#fff;padding:1px 6px;border-radius:9px;margin-left:4px;font-weight:600;">ON</span>
                @else
                    <span style="font-size:10px;background:#6b7280;color:#fff;padding:1px 6px;border-radius:9px;margin-left:4px;font-weight:600;">OFF</span>
                @endif
            </button>
        </form>

        <div class="admin-nav-head" style="margin-top:auto;">Compte</div>
        <a href="{{ route('member.dashboard') }}" class="admin-nav-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l9-9 9 9"/><path d="M5 10v10h14V10"/></svg>
            Espace adhérent
        </a>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="admin-nav-link" style="width:100%;background:none;border:none;text-align:left;cursor:pointer;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Déconnexion
            </button>
        </form>
    </aside>

    <main class="admin-main">
        @if (session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif
        {{ $slot }}
    </main>
</div>

</body>
</html>
