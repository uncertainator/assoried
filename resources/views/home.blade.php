<x-layouts.app title="La Fabrique — Association citoyenne">

{{-- Hero --}}
<section class="fb-hero">
    <div class="fb-hero-pattern" aria-hidden="true"></div>
    <div class="fb-hero-content">
        <div class="fb-eyebrow">Territoires Alsace Centrale</div>
        <h1 class="fb-h1">
            Association Citoyenne de <em class="fb-italic-accent">Liens</em> & de <em class="fb-italic-accent">Projets</em>.
        </h1>
        <p class="fb-lead" style="max-width:52ch;">
            On améliore le cadre de vie ensemble par des activités, des projets, et l'envie de bien vivre dans notre territoire.
        </p>
        <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;">
            <a href="{{ route('activities') }}" class="fb-btn fb-btn-primary fb-btn-lg">Découvrir nos activités</a>
            <a href="{{ route('inscription') }}" class="fb-btn fb-btn-outline fb-btn-lg">Devenir adhérent</a>
        </div>
        <div class="fb-hero-meta">
            <span><strong>4</strong> piliers</span>
            <span class="fb-meta-dot">·</span>
            <span><strong>{{ $circles->count() }}</strong> cercles</span>
            <span class="fb-meta-dot">·</span>
        </div>
    </div>
    <div class="fb-hero-side">
        @if ($heroEvent)
            @php
                $heroColor = match($heroEvent->tag) {
                    'Atelier'     => ['border' => 'var(--brique-500)', 'tag' => 'var(--brique-600)'],
                    'Information' => ['border' => 'var(--ocre-400)',   'tag' => 'var(--ocre-600)'],
                    default       => ['border' => 'var(--mousse-500)', 'tag' => 'var(--mousse-500)'],
                };
            @endphp
            <article class="fb-hero-card fb-event-card" style="border-top-color:{{ $heroColor['border'] }};">
                <div class="fb-event-tag" style="color:{{ $heroColor['tag'] }};text-transform:uppercase;">
                    {{ $heroEvent->tag ?? $heroEvent->circle->name }}
                </div>
                <h3 class="fb-event-title">{{ $heroEvent->title }}</h3>
                @if ($heroEvent->description)
                    <p class="fb-event-body">{{ $heroEvent->description }}</p>
                @endif
                <div class="fb-event-meta">
                    <span class="fb-mono">{{ $heroEvent->starts_at->isoFormat('D MMMM YYYY · HH:mm') }}</span>
                    @if ($heroEvent->location)
                        <span class="fb-event-place">{{ $heroEvent->location }}</span>
                    @endif
                </div>
                @if ($heroEvent->foot_type)
                    <div class="fb-event-foot">
                        @if ($heroEvent->foot_type === 'places_limitees')
                            <span class="fb-badge fb-badge-mousse">Places limitées</span>
                        @else
                            <span class="fb-badge fb-badge-ocre">Entrée libre</span>
                        @endif
                        <a href="{{ route('evenements.show', $heroEvent) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Je m'inscris →</a>
                    </div>
                @else
                    <a href="{{ route('evenements.show', $heroEvent) }}" class="fb-btn fb-btn-primary fb-btn-block" style="white-space:nowrap;margin-top:16px;">Je m'inscris →</a>
                @endif
            </article>
        @else
            <div class="fb-hero-card">
                <div class="fb-eyebrow">Prochain rendez-vous</div>
                <p style="color:var(--fg-tertiary);font-size:15px;">Aucun événement public à venir pour le moment.</p>
            </div>
        @endif
    </div>
</section>

{{-- Piliers --}}
<section class="fb-section">
    <div class="fb-section-head">
        <div class="fb-eyebrow">Ce qu'on fait</div>
        <h2 class="fb-h2">Quatre piliers, un même fil rouge</h2>
    </div>
    <div class="fb-piliers-grid">
        <article class="fb-pilier">
            <div class="fb-pilier-icon">
                <img src="/images/pilier-bien-vivre.svg" alt="" width="48" height="48">
            </div>
            <h3 class="fb-pilier-name">Bien vivre</h3>
            <p class="fb-pilier-desc">Des projets pour mieux vivre entre générations et cultures.</p>
            <!--<a href="{{ route('inscription') }}" class="fb-pilier-link">En savoir plus →</a>-->
        </article>
        <article class="fb-pilier">
            <div class="fb-pilier-icon">
                <img src="/images/pilier-ecologie.svg" alt="" width="48" height="48">
            </div>
            <h3 class="fb-pilier-name">Citoyenneté</h3>
            <p class="fb-pilier-desc">Des initiatives pour renforcer le tissu social et la participation citoyenne.</p>
            <!--<a href="{{ route('inscription') }}" class="fb-pilier-link">En savoir plus →</a>-->
        </article>
        <article class="fb-pilier">
            <div class="fb-pilier-icon">
                <img src="/images/pilier-fabrique.svg" alt="" width="48" height="48">
            </div>
            <h3 class="fb-pilier-name">Partage de la connaissance</h3>
            <p class="fb-pilier-desc">Vous avez une idée, un projet ? On vous aide à la mettre en route.</p>
            <!--<a href="{{ route('inscription') }}" class="fb-pilier-link">En savoir plus →</a>-->
        </article>
        <article class="fb-pilier">
            <div class="fb-pilier-icon">
                <img src="/images/pilier-cooperative.svg" alt="" width="48" height="48">
            </div>
            <h3 class="fb-pilier-name">Développement local</h3>
            <p class="fb-pilier-desc">Des structures pour soutenir l'entrepreneuriat local.</p>
            <!--<a href="{{ route('inscription') }}" class="fb-pilier-link">En savoir plus →</a>-->
        </article>
    </div>
</section>

{{-- Events placeholder --}}
<section class="fb-section-creme">
    <div style="max-width:1280px;margin:0 auto;padding:0;">
        <div class="fb-section-head">
            <div class="fb-eyebrow">Agenda</div>
            <h2 class="fb-h2">À venir, près de chez vous</h2>
            <a href="{{ route('evenements') }}" class="fb-section-link">Voir tout l'agenda →</a>
        </div>
        <div class="fb-events-grid">
            @forelse ($upcomingEvents as $event)
                @php
                    $color = match($event->tag) {
                        'Atelier'     => ['border' => 'var(--brique-500)', 'tag' => 'var(--brique-600)'],
                        'Information' => ['border' => 'var(--ocre-400)',   'tag' => 'var(--ocre-600)'],
                        default       => ['border' => 'var(--mousse-500)', 'tag' => 'var(--mousse-500)'],
                    };
                @endphp
                <article class="fb-event-card" style="border-top-color:{{ $color['border'] }};">
                    <div class="fb-event-tag" style="color:{{ $color['tag'] }};text-transform:uppercase;">
                        {{ $event->tag ?? $event->circle->name }}
                    </div>
                    <h3 class="fb-event-title">{{ $event->title }}</h3>
                    @if ($event->description)
                        <p class="fb-event-body">{{ $event->description }}</p>
                    @endif
                    <div class="fb-event-meta">
                        <span class="fb-mono">{{ $event->starts_at->isoFormat('D MMMM YYYY · HH:mm') }}</span>
                        @if ($event->location)
                            <span class="fb-event-place">{{ $event->location }}</span>
                        @endif
                    </div>
                    <div class="fb-event-foot">
                        @if ($event->foot_type === 'places_limitees')
                            <span class="fb-badge fb-badge-mousse">Places limitées</span>
                        @elseif ($event->foot_type === 'entree_libre')
                            <span class="fb-badge fb-badge-ocre">Entrée libre</span>
                        @else
                            <span></span>
                        @endif
                        <a href="{{ route('evenements.show', $event) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Je m'inscris →</a>
                    </div>
                </article>
            @empty
                <p style="color:var(--fg-tertiary);font-size:15px;">Aucun événement public à venir pour le moment.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- Testimonial --}}
<section class="fb-section-brique">
    <div class="fb-testimonial">
        <div class="fb-quote-mark">«</div>
        <blockquote class="fb-quote-text">
            On a commencé à parler avenir autour d'un café. Quelques mois plus tard, c'est une liste d'initiatives citoyennes qui mobilise et qui nourrit l'envie de faire ensemble.
        </blockquote>
        <div class="fb-quote-author">
            <span class="fb-quote-name">Jonas V.</span>
            <span class="fb-quote-role">— habitant, membre fondateur</span>
        </div>
    </div>
</section>

{{-- Consultations publiques --}}
@if ($consultations->isNotEmpty())
<section class="fb-section-creme">
    <div style="max-width:1280px;margin:0 auto;padding:0;">
        <div class="fb-section-head">
            <div class="fb-eyebrow">Participation</div>
            <h2 class="fb-h2">Donnez votre avis</h2>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;">
            @foreach ($consultations as $consultation)
                <article style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:12px;padding:20px 24px;display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span class="{{ $consultation->mode_recueil->badgeClass() }}" style="font-size:11px;font-weight:600;padding:2px 9px;border-radius:20px;">
                            {{ $consultation->mode_recueil->label() }}
                        </span>
                        @if ($consultation->date_cloture)
                            <span style="font-size:11px;color:var(--fg-tertiary);">
                                Jusqu'au {{ $consultation->date_cloture->translatedFormat('j M Y') }}
                            </span>
                        @endif
                    </div>
                    <h3 style="font-size:16px;font-weight:600;color:var(--fg-primary);margin:0;line-height:1.4;">
                        {{ $consultation->titre }}
                    </h3>
                    @if ($consultation->description)
                        <p style="font-size:14px;color:var(--fg-secondary);margin:0;line-height:1.6;">
                            {{ Str::limit($consultation->description, 120) }}
                        </p>
                    @endif
                    <div style="display:flex;gap:8px;margin-top:auto;">
                        <a href="{{ route('consultations.show', $consultation) }}" class="fb-btn fb-btn-primary fb-btn-sm">
                            Participer →
                        </a>
                        <a href="{{ route('consultations.resultats', $consultation) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
                            Résultats
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="fb-section">
    <div class="fb-cta">
        <div>
            <div class="fb-eyebrow">Adhérer</div>
            <h2 class="fb-h2">Rejoignez Hop'Initiatives</h2>
            <p class="fb-lead">Pour soutenir, et pour participer aux décisions — ensemble.</p>
        </div>
        <div class="fb-cta-actions">
            <a href="{{ route('inscription') }}" class="fb-btn fb-btn-primary fb-btn-lg">Devenir adhérent →</a>
            <a href="{{ route('login') }}" class="fb-link-quiet">ou se connecter si déjà adhérent</a>
        </div>
    </div>
</section>

</x-layouts.app>
