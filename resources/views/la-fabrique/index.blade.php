<x-layouts.app title="Espace La Fabrique — La Fabrique">

{{-- ═══════════════════════════════════════ SECTION 1 — HERO ══ --}}
<section class="fb-hero">
    <div class="fb-hero-pattern" aria-hidden="true"></div>

    <div class="fb-hero-content">
        <div class="fb-eyebrow">Espace La Fabrique</div>
        <h1 class="fb-h1">Un espace pour expérimenter, co-construire et faire avancer vos projets.</h1>
        <p class="fb-lead" style="max-width:54ch;margin-top:8px;">
            Vous avez un projet citoyen, personnel ou professionnel ?
            Trouvez ici l'accompagnement qui vous correspond.
        </p>
        <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;">
            <a href="#citoyen" class="fb-btn fb-btn-primary fb-btn-lg">J'ai un projet citoyen ou personnel</a>
            <a href="#entreprises" class="fb-btn fb-btn-outline fb-btn-lg">Mon entreprise cherche un accompagnement</a>
        </div>
    </div>

    <div class="fb-hero-side">
        <x-image-placeholder label="Photo atelier — hero" ratio="16:9" style="width:100%;" />
    </div>
</section>

{{-- ═══════════════════════════ SECTION 2 — CITOYEN / PERSO ══ --}}
<section class="fb-section" id="citoyen" x-data="{ open: false }">
    <div class="lf-section-grid">
        <div>
            <span class="fb-badge fb-badge-mousse" style="margin-bottom:16px;">Citoyen &amp; Projets personnels</span>
            <h2 class="fb-h2">Vous portez un projet qui vous tient à cœur ?</h2>
            <p class="fb-body" style="margin-top:20px;max-width:48ch;">
                Initiative citoyenne, projet associatif, idée à tester ou à structurer —
                l'accompagnement est gratuit pour les particuliers et les collectifs.
            </p>
            <div style="margin-top:32px;display:flex;flex-direction:column;gap:12px;align-items:flex-start;">
                <a href="#form-citoyen" class="fb-btn fb-btn-primary"
                   @click.prevent="open = true; $nextTick(() => document.getElementById('form-citoyen').scrollIntoView({ behavior: 'smooth' }))">Déposer mon projet</a>
                <a href="{{ route('parcours.start') }}" class="fb-link-quiet">
                    Pas sûr·e par où commencer ? Suivez le parcours guidé →
                </a>
            </div>
        </div>
        <div class="lf-images-grid">
            <x-image-placeholder label="Photo atelier citoyen 1" ratio="1:1" />
            <x-image-placeholder label="Photo atelier citoyen 2" ratio="1:1" />
        </div>
    </div>

    <div id="form-citoyen" style="margin-top:48px;" x-show="open" x-cloak>
        @include('lab.external._form-citoyen')
    </div>
</section>

{{-- ══════════════════════════ SECTION 3 — ENTREPRISES ══ --}}
<div class="fb-section-creme">
    <section class="fb-section" id="entreprises" x-data="{ open: false }">
        <div class="lf-section-grid">
            <div class="lf-images-grid">
                <x-image-placeholder label="Photo atelier entreprise 1" ratio="1:1" />
                <x-image-placeholder label="Photo atelier entreprise 2" ratio="1:1" />
            </div>
            <div>
                <span class="fb-badge fb-badge-ocre" style="margin-bottom:16px;">Entreprises &amp; Structures</span>
                <h2 class="fb-h2">Votre organisation a besoin d'un regard extérieur ?</h2>
                <p class="fb-body" style="margin-top:20px;max-width:48ch;">
                    Design thinking, intelligence collective, stratégie, gestion de projet —
                    des expertises mobilisables sur mesure pour vos équipes et vos enjeux.
                </p>
                <div style="margin-top:32px;display:flex;flex-direction:column;gap:12px;align-items:flex-start;">
                    <a href="#form-entreprise" class="fb-btn fb-btn-primary"
                       @click.prevent="open = true; $nextTick(() => document.getElementById('form-entreprise').scrollIntoView({ behavior: 'smooth' }))">Faire appel au Lab</a>
                    <a href="{{ route('parcours.start') }}" class="fb-link-quiet">
                        Pas sûr·e par où commencer ? Suivez le parcours guidé →
                    </a>
                </div>
            </div>
        </div>

        <div id="form-entreprise" style="margin-top:48px;" x-show="open" x-cloak>
            @include('lab.external._form-entreprise')
        </div>
    </section>
</div>

{{-- ══════════════════ SECTION 4 — DÉCLENCHEUR PARCOURS ══ --}}
<section class="fb-section-brique">
    <div class="fb-testimonial">
        <h2 style="font:600 var(--text-3xl)/var(--leading-tight) var(--font-display);
                    color:var(--creme-50);letter-spacing:var(--tracking-tight);
                    text-wrap:balance;margin:0 0 20px;">
            Vous ne savez pas encore par où commencer ?
        </h2>
        <p style="font:400 var(--text-md)/var(--leading-relaxed) var(--font-display);
                   color:var(--brique-200);max-width:52ch;margin:0 auto 36px;text-wrap:pretty;">
            En 3 questions, trouvez l'accompagnement qui correspond à votre situation.
        </p>
        <a href="{{ route('parcours.start') }}" class="fb-btn fb-btn-primary fb-btn-lg" style="position:relative;z-index:1;">
            Démarrer le parcours guidé
        </a>
        <div style="margin-top:40px;max-width:480px;margin-left:auto;margin-right:auto;">
            <x-image-placeholder label="Photo parcours guidé" ratio="16:9" />
        </div>
    </div>
</section>

</x-layouts.app>
