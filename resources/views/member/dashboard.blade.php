<x-layouts.member title="Tableau de bord — La Fabrique">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Bonjour <em>{{ $user->name ?: explode('@', $user->email)[0] }}</em>,</h1>
        <div class="ea-greeting-sub">Bienvenue dans votre espace adhérent.</div>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('member.circles.index') }}" class="fb-btn fb-btn-primary fb-btn-sm">
            Gérer mes cercles →
        </a>
    </div>
</div>

<div class="ea-stats">
    <div class="ea-stat">
        <div class="ea-stat-label">Mes cercles</div>
        <div class="ea-stat-val">{{ $user->circles->count() }}</div>
        <div class="ea-stat-trend">{{ $user->circles->count() > 0 ? 'Actif' : 'Aucun pour l\'instant' }}</div>
    </div>
    <div class="ea-stat">
        <div class="ea-stat-label">Compte</div>
        <div class="ea-stat-val" style="font-size:18px;">{{ ucfirst($user->role->value) }}</div>
        <div class="ea-stat-trend">Adhérent depuis {{ $user->created_at->translatedFormat('M Y') }}</div>
    </div>
    <!--<div class="ea-stat">
        <div class="ea-stat-label">Événements</div>
        <div class="ea-stat-val">—</div>
        <div class="ea-stat-trend">À venir</div>
    </div>-->
    <!--<div class="ea-stat">
        <div class="ea-stat-label">Projets</div>
        <div class="ea-stat-val">—</div>
        <div class="ea-stat-trend">À venir</div>
    </div>-->
</div>
<div class="ea-cols">
    {{-- Colonne principale : next events  --}}
        <div class="ea-panel">
            <div class="ea-panel-head">
                <h2 class="ea-panel-title">Prochains événements</h2>
            </div>
        @php
            $sidebarNextEvents = \App\Models\Event::upcoming()->limit(2)->with('circle')->get();
        @endphp
        @if ($sidebarNextEvents->isNotEmpty())
            @foreach ($sidebarNextEvents as $sidebarEvent)
            <div class="ea-event-row">
                <div class="ea-event-info">
                    <div class="ea-event-name">{{ $sidebarEvent->title }}</div>
                    <div class="ea-event-meta-2">
                        {{ $sidebarEvent->starts_at->translatedFormat('d M à H:i') }} · {{ $sidebarEvent->circle->name }}
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;margin:0;">Aucun événement à venir.</p>
        @endif

           
    </div>
    {{-- Colonne principale : feed unifié --}}
    <div>
        <div class="ea-panel">
            <div class="ea-panel-head">
                <h2 class="ea-panel-title">Actualités</h2>
                <a href="{{ route('member.feed') }}" class="ea-panel-link">Publications →</a>
            </div>

            @forelse ($feed as $post)
                <div class="ea-feed-item">
                    <div class="ea-feed-item-meta">
                        <span class="ea-feed-source-badge {{ $post->pushed_to_general ? 'ea-feed-source-general' : 'ea-feed-source-circle' }}">
                            {{ $post->pushed_to_general ? 'Général' : $post->circle->name }}
                        </span>
                        <span class="ea-feed-item-date">{{ $post->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="ea-feed-item-author">{{ $post->author?->name ?? 'Auteur supprimé' }}</div>
                    <p class="ea-feed-item-body">{{ Str::limit($post->body, 300) }}</p>
                    <a href="{{ route('member.circles.show', $post->circle) }}" class="ea-feed-item-link">
                        Voir le feed complet →
                    </a>
                </div>
            @empty
                <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;margin:0;">
                    Aucune publication pour le moment.
                    @if ($user->circles->isEmpty())
                        <a href="{{ route('member.circles.index') }}" style="color:var(--brique-600);">Rejoindre un cercle →</a>
                    @endif
                </p>
            @endforelse

            @if ($feed->hasPages())
                <div style="margin-top:16px;border-top:1px solid var(--border-subtle);padding-top:16px;">
                    {{ $feed->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Colonne 2 : Mes cercles + Mes demandes --}}
    <div>

        {{-- Consultations ouvertes --}}
        @if ($consultationsOuvertes->isNotEmpty())
        <div class="ea-panel" style="margin-bottom:16px;">
            <div class="ea-panel-head">
                <h2 class="ea-panel-title">Consultations ouvertes</h2>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach ($consultationsOuvertes as $consultation)
                    <div style="border:1px solid var(--border-subtle);border-radius:8px;padding:12px 14px;">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                            <span class="{{ $consultation->mode_recueil->badgeClass() }}" style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;">
                                {{ $consultation->mode_recueil->label() }}
                            </span>
                            @if ($consultation->date_cloture)
                                <span style="font-size:11px;color:var(--fg-tertiary);">jusqu'au {{ $consultation->date_cloture->translatedFormat('j M') }}</span>
                            @endif
                        </div>
                        <div style="font-size:14px;font-weight:600;margin-bottom:8px;">{{ $consultation->titre }}</div>
                        <a href="{{ route('consultations.show', $consultation) }}" class="fb-btn fb-btn-outline fb-btn-sm">
                            Participer →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Mes cercles (agrandi avec aperçu événements) --}}
        <div class="ea-panel" style="margin-bottom:16px;">
            <div class="ea-panel-head">
                <h2 class="ea-panel-title">Mes cercles</h2>
                <a href="{{ route('member.circles.index') }}" class="ea-panel-link">Gérer →</a>
            </div>
            @if ($user->circles->isEmpty())
                <p style="font-size:13px;color:var(--fg-tertiary);margin:0;">
                    Vous n'avez pas encore rejoint de cercle.
                    <a href="{{ route('member.circles.index') }}" style="color:var(--brique-600);">Découvrir →</a>
                </p>
            @else
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach ($user->circles as $circle)
                        @php $nextEvents = $upcomingEventsByCircle[$circle->id] ?? collect(); @endphp
                        <div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:8px;padding:14px 16px;">
                            <div style="font-size:14px;font-weight:600;margin-bottom:8px;">{{ $circle->name }}</div>
                            @if ($nextEvents->isEmpty())
                                <div style="font-size:12px;color:var(--fg-tertiary);font-style:italic;margin-bottom:10px;">Aucun événement à venir</div>
                            @else
                                <div style="margin-bottom:10px;display:flex;flex-direction:column;gap:4px;">
                                    @foreach ($nextEvents as $event)
                                        <div style="font-size:12px;color:var(--fg-secondary);">
                                            <span style="font-weight:500;">{{ $event->title }}</span>
                                            <span style="color:var(--fg-tertiary);"> — {{ $event->starts_at->translatedFormat('d M Y') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <a href="{{ route('member.circles.show', $circle) }}" class="fb-btn fb-btn-outline fb-btn-sm">
                                Accéder au cercle →
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Mes demandes (compact, conditionnel) --}}
        @if ($recentMemberships->isNotEmpty())
        <div class="ea-panel ea-panel--compact" style="margin-bottom:16px;">
            <div class="ea-panel-head">
                <h2 class="ea-panel-title">Mes demandes</h2>
            </div>
            @foreach ($recentMemberships as $membership)
                <div class="ea-event-row" style="align-items:flex-start;">
                    <div style="flex:1;">
                        <div class="ea-event-name">{{ $membership->circle->name }}</div>
                        @if ($membership->status->value === 'pending')
                            <div class="ea-event-meta-2">
                                <span class="fb-badge fb-badge-ocre">En attente</span>
                            </div>
                        @elseif ($membership->status->value === 'approved')
                            <div class="ea-event-meta-2">
                                <span class="fb-badge fb-badge-mousse">Acceptée</span>
                            </div>
                        @else
                            <div class="ea-event-meta-2">
                                <span class="fb-badge fb-badge-brique">Refusée</span>
                                @if ($membership->rejection_reason)
                                    <span style="font-size:11px;color:var(--fg-tertiary);margin-left:6px;">
                                        {{ $membership->rejection_reason }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                    @if ($membership->status->value === 'pending')
                        <form method="POST" action="{{ route('member.circles.cancel', $membership->circle) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm"
                                    onclick="return confirm('Annuler cette demande ?')">
                                Annuler
                            </button>
                        </form>
                    @endif
                </div>

                @foreach (Auth::user()->unreadNotifications->where('data->circle_name', $membership->circle->name) as $notif)
                    <form method="POST" action="{{ route('member.notifications.read', $notif->id) }}"
                          style="display:none;" id="notif-read-{{ $notif->id }}">
                        @csrf
                    </form>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            document.getElementById('notif-read-{{ $notif->id }}').submit();
                        });
                    </script>
                @endforeach
            @endforeach
        </div>
        @endif

    </div>{{-- /Colonne 2 --}}

    {{-- Colonne 3 : À découvrir + Mon compte --}}
    <!--<div>

        {{-- À découvrir --}}
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
                <a href="{{ route('evenements') }}" class="ea-quick-action" style="color:var(--mousse-600);">
                    En savoir plus →
                </a>
            </div>
        </div>

        {{-- Mon compte --}}
        <div class="ea-panel">
            <div class="ea-panel-head">
                <h2 class="ea-panel-title">Mon compte</h2>
            </div>
            <div style="font-size:14px;color:var(--fg-secondary);">
                <div style="margin-bottom:8px;"><strong>Email :</strong> {{ $user->email }}</div>
                <div><strong>Nom :</strong> {{ $user->name ?: 'Non renseigné' }}</div>
            </div>
            <div style="margin-top:16px;border-top:1px solid var(--border-subtle);padding-top:16px;">
                <a href="{{ route('member.profile') }}" class="fb-btn fb-btn-outline fb-btn-sm">
                    Mon profil
                </a>
            </div>
        </div>

    </div>-->
</div>

</x-layouts.member>
