<x-layouts.app title="La Fabrique — Association citoyenne">

{{-- Hero --}}
<section class="fb-hero">
    <div class="fb-hero-pattern" aria-hidden="true"></div>
    <div class="fb-hero-content">
        <div class="fb-eyebrow">Association citoyenne · Alsace</div>
        <h1 class="fb-h1">
            Une fabrique <em class="fb-italic-accent">à idées,</em><br>
            entre voisins.
        </h1>
        <p class="fb-lead" style="max-width:52ch;">
            On améliore le cadre de vie ensemble — par des activités, des projets, et l'envie de bien vivre dans notre ville.
        </p>
        <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;">
            <a href="{{ route('inscription') }}" class="fb-btn fb-btn-primary fb-btn-lg">Découvrir nos activités</a>
            <a href="{{ route('inscription') }}" class="fb-btn fb-btn-outline fb-btn-lg">Devenir adhérent</a>
        </div>
        <div class="fb-hero-meta">
            <span><strong>4</strong> piliers</span>
            <span class="fb-meta-dot">·</span>
            <span><strong>{{ $circles->count() }}</strong> cercles</span>
            <span class="fb-meta-dot">·</span>
            <span><strong>En cours</strong> de constitution</span>
        </div>
    </div>
    <div class="fb-hero-side">
        <div class="fb-hero-card">
            <div class="fb-eyebrow">Prochain rendez-vous</div>
            <h3 class="fb-card-title">Café des idées</h3>
            <p class="fb-card-body">Vous avez une envie, un projet ? On en discute autour d'un café.</p>
            <div class="fb-card-meta">À confirmer · Rejoignez la liste</div>
            <a href="{{ route('inscription') }}" class="fb-btn fb-btn-primary fb-btn-block" style="white-space:nowrap;">Je viens →</a>
        </div>
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
            <p class="fb-pilier-desc">Animations, ateliers et moments partagés pour mieux se connaître entre voisins.</p>
            <a href="{{ route('inscription') }}" class="fb-pilier-link">En savoir plus →</a>
        </article>
        <article class="fb-pilier">
            <div class="fb-pilier-icon">
                <img src="/images/pilier-ecologie.svg" alt="" width="48" height="48">
            </div>
            <h3 class="fb-pilier-name">Écologie & sécurité</h3>
            <p class="fb-pilier-desc">Compost partagé, jardin de quartier, vigilance — agir au quotidien.</p>
            <a href="{{ route('inscription') }}" class="fb-pilier-link">En savoir plus →</a>
        </article>
        <article class="fb-pilier">
            <div class="fb-pilier-icon">
                <img src="/images/pilier-fabrique.svg" alt="" width="48" height="48">
            </div>
            <h3 class="fb-pilier-name">Fabrique à projets</h3>
            <p class="fb-pilier-desc">Vous avez une idée ? On vous aide à la mettre en route.</p>
            <a href="{{ route('inscription') }}" class="fb-pilier-link">En savoir plus →</a>
        </article>
        <article class="fb-pilier">
            <div class="fb-pilier-icon">
                <img src="/images/pilier-cooperative.svg" alt="" width="48" height="48">
            </div>
            <h3 class="fb-pilier-name">Coopérative</h3>
            <p class="fb-pilier-desc">Conciergerie et CAE pour soutenir l'entrepreneuriat local.</p>
            <a href="{{ route('inscription') }}" class="fb-pilier-link">En savoir plus →</a>
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
            <article class="fb-event-card" style="border-top-color:var(--brique-500);">
                <div class="fb-event-tag" style="color:var(--brique-600);">ATELIER · À confirmer</div>
                <h3 class="fb-event-title">Réparer son vélo, entre voisins</h3>
                <p class="fb-event-body">On apporte sa monture, on partage les outils. Pas besoin d'être bricoleur.</p>
                <div class="fb-event-meta">
                    <span class="fb-mono">Date à confirmer · 19:00</span>
                    <span class="fb-event-place">Lieu à définir</span>
                </div>
                <div class="fb-event-foot">
                    <span class="fb-badge fb-badge-mousse">Places limitées</span>
                    <a href="{{ route('inscription') }}" class="fb-btn fb-btn-ghost fb-btn-sm">Je m'inscris →</a>
                </div>
            </article>
            <article class="fb-event-card" style="border-top-color:var(--ocre-400);">
                <div class="fb-event-tag" style="color:var(--ocre-600);">FABRIQUE · À confirmer</div>
                <h3 class="fb-event-title">Café des idées</h3>
                <p class="fb-event-body">Vous avez un projet, une envie ? On en discute autour d'un café.</p>
                <div class="fb-event-meta">
                    <span class="fb-mono">Date à confirmer · 09:30</span>
                    <span class="fb-event-place">Lieu à définir</span>
                </div>
                <div class="fb-event-foot">
                    <span class="fb-badge fb-badge-ocre">Ouvert à tous</span>
                    <a href="{{ route('inscription') }}" class="fb-btn fb-btn-ghost fb-btn-sm">Je m'inscris →</a>
                </div>
            </article>
            <article class="fb-event-card" style="border-top-color:var(--mousse-500);">
                <div class="fb-event-tag" style="color:var(--mousse-500);">ÉCOLOGIE · À confirmer</div>
                <h3 class="fb-event-title">Compost partagé, mode d'emploi</h3>
                <p class="fb-event-body">Visite du nouveau composteur de quartier. Comment l'utiliser, qu'y mettre.</p>
                <div class="fb-event-meta">
                    <span class="fb-mono">Date à confirmer · 11:00</span>
                    <span class="fb-event-place">Lieu à définir</span>
                </div>
                <div class="fb-event-foot">
                    <span class="fb-badge fb-badge-mousse">Entrée libre</span>
                    <a href="{{ route('inscription') }}" class="fb-btn fb-btn-ghost fb-btn-sm">Je m'inscris →</a>
                </div>
            </article>
        </div>
    </div>
</section>

{{-- Testimonial --}}
<section class="fb-section-brique">
    <div class="fb-testimonial">
        <div class="fb-quote-mark">«</div>
        <blockquote class="fb-quote-text">
            On a commencé à trois autour d'un compost. Six mois plus tard, c'est un jardin de quartier qui nourrit huit familles. La Fabrique, c'est ça : on commence petit, on fait ensemble.
        </blockquote>
        <div class="fb-quote-author">
            <span class="fb-quote-name">Anne B.</span>
            <span class="fb-quote-role">— habitante, membre fondatrice</span>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="fb-section">
    <div class="fb-cta">
        <div>
            <div class="fb-eyebrow">Adhérer</div>
            <h2 class="fb-h2">Rejoignez La Fabrique</h2>
            <p class="fb-lead">Pour soutenir, et pour participer aux décisions — ensemble.</p>
        </div>
        <div class="fb-cta-actions">
            <a href="{{ route('inscription') }}" class="fb-btn fb-btn-primary fb-btn-lg">Devenir adhérent →</a>
            <a href="{{ route('login') }}" class="fb-link-quiet">ou se connecter si déjà adhérent</a>
        </div>
    </div>
</section>

</x-layouts.app>
