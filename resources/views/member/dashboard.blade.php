<x-layouts.member title="Tableau de bord — La Fabrique">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">
            Bonjour <em>{{ $user->name ?: explode('@', $user->email)[0] }}</em>,
        </h1>
        <div class="ea-greeting-sub">Bienvenue dans votre espace adhérent.</div>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('member.circles.index') }}" class="fb-btn fb-btn-primary fb-btn-sm">Gérer mes cercles →</a>
    </div>
</div>

{{-- Stats --}}
<div class="ea-stats">
    <div class="ea-stat">
        <div class="ea-stat-label">Mes cercles</div>
        <div class="ea-stat-val">{{ $user->circles->count() }}</div>
        <div class="ea-stat-trend">{{ $user->circles->count() > 0 ? 'Actif' : 'Aucun pour l\'instant' }}</div>
    </div>
    <div class="ea-stat">
        <div class="ea-stat-label">Compte</div>
        <div class="ea-stat-val" style="font-size:1.4rem;">Adhérent</div>
        <div class="ea-stat-trend">Depuis {{ $user->created_at->translatedFormat('M Y') }}</div>
    </div>
    <div class="ea-stat">
        <div class="ea-stat-label">Événements</div>
        <div class="ea-stat-val">—</div>
        <div class="ea-stat-trend" style="color:var(--fg-tertiary);">À venir</div>
    </div>
    <div class="ea-stat">
        <div class="ea-stat-label">Projets</div>
        <div class="ea-stat-val">—</div>
        <div class="ea-stat-trend" style="color:var(--fg-tertiary);">À venir</div>
    </div>
</div>

{{-- Content columns --}}
<div class="ea-cols">
    {{-- Left column --}}
    <div>
        {{-- My circles --}}
        <div class="ea-panel" style="margin-bottom:16px;">
            <div class="ea-panel-head">
                <h2 class="ea-panel-title">Mes cercles</h2>
                <a href="{{ route('member.circles.index') }}" class="ea-panel-link">Gérer →</a>
            </div>
            @if ($user->circles->isEmpty())
                <p style="font-size:14px;color:var(--fg-tertiary);margin:0;">
                    Vous n'avez pas encore rejoint de cercle.
                    <a href="{{ route('member.circles.index') }}" style="color:var(--brique-600);">Découvrir les cercles →</a>
                </p>
            @else
                @foreach ($user->circles as $circle)
                    <div class="ea-event-row">
                        <div style="flex:1;">
                            <div class="ea-event-name">{{ $circle->name }}</div>
                            <div class="ea-event-meta-2">{{ Str::limit($circle->description, 60) }}</div>
                        </div>
                        <form method="POST" action="{{ route('member.circles.leave', $circle) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm"
                                    onclick="return confirm('Quitter ce cercle ?')">Quitter</button>
                        </form>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Mes demandes --}}
        @if ($recentMemberships->isNotEmpty())
        <div class="ea-panel">
            <div class="ea-panel-head">
                <h2 class="ea-panel-title">Mes demandes</h2>
            </div>
            @foreach ($recentMemberships as $membership)
                <div class="ea-event-row" style="align-items:flex-start;">
                    <div style="flex:1;">
                        <div class="ea-event-name">{{ $membership->circle->name }}</div>
                        @if ($membership->status->value === 'pending')
                            <div class="ea-event-meta-2">
                                <span class="fb-badge fb-badge-ocre">En attente de validation</span>
                            </div>
                        @elseif ($membership->status->value === 'approved')
                            <div class="ea-event-meta-2">
                                <span class="fb-badge fb-badge-mousse">Acceptée</span>
                            </div>
                        @else
                            <div class="ea-event-meta-2">
                                <span class="fb-badge fb-badge-brique">Refusée</span>
                                @if ($membership->rejection_reason)
                                    <span style="font-size:12px;color:var(--fg-tertiary);margin-left:8px;">{{ $membership->rejection_reason }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    @if ($membership->status->value === 'pending')
                        <form method="POST" action="{{ route('member.circles.cancel', $membership->circle) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm"
                                    onclick="return confirm('Annuler cette demande ?')">Annuler</button>
                        </form>
                    @endif
                </div>

                {{-- Marquer les notifications DB liées comme lues --}}
                @foreach (Auth::user()->unreadNotifications->where('data->circle_name', $membership->circle->name) as $notif)
                    <form method="POST" action="{{ route('member.notifications.read', $notif->id) }}" style="display:none;" id="notif-read-{{ $notif->id }}">
                        @csrf
                    </form>
                    <script>document.addEventListener('DOMContentLoaded',function(){document.getElementById('notif-read-{{ $notif->id }}').submit();});</script>
                @endforeach
            @endforeach
        </div>
        @endif
    </div>

    {{-- Right column --}}
    <div>
        <div class="ea-panel" style="margin-bottom:16px;">
            <div class="ea-panel-head">
                <h2 class="ea-panel-title">À découvrir</h2>
            </div>
            <div class="ea-quick-card">
                <div class="ea-quick-eyebrow">Cercles</div>
                <h3 class="ea-quick-title">Rejoindre un cercle</h3>
                <p class="ea-quick-desc">Trouvez le groupe qui correspond à vos envies.</p>
                <a href="{{ route('member.circles.index') }}" class="ea-quick-action">Voir les cercles →</a>
            </div>
            <div class="ea-quick-card" style="margin-bottom:0;background:var(--mousse-100);border-color:var(--mousse-200);">
                <div class="ea-quick-eyebrow" style="color:var(--mousse-600);">Événements</div>
                <h3 class="ea-quick-title">Agenda à venir</h3>
                <p class="ea-quick-desc">Les activités de La Fabrique arrivent bientôt.</p>
                <a href="{{ route('evenements') }}" class="ea-quick-action" style="color:var(--mousse-600);">En savoir plus →</a>
            </div>
        </div>

        <div class="ea-panel">
            <div class="ea-panel-head">
                <h2 class="ea-panel-title">Mon compte</h2>
            </div>
            <div style="font-size:14px;color:var(--fg-secondary);">
                <div style="margin-bottom:8px;"><strong>Email :</strong> {{ $user->email }}</div>
                <div><strong>Nom :</strong> {{ $user->name ?: 'Non renseigné' }}</div>
            </div>
            <div style="margin-top:16px;border-top:1px solid var(--border-subtle);padding-top:16px;">
                <a href="{{ route('member.profile') }}" class="fb-btn fb-btn-outline fb-btn-sm" style="margin-right:8px;">
                    Mon profil
                </a>
            </div>
        </div>
    </div>
</div>

</x-layouts.member>
