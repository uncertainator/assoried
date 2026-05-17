<x-layouts.app title="Orientation — La Fabrique">

<section class="fb-section" style="padding-top:64px;padding-bottom:80px;">
    <div style="max-width:640px;margin:0 auto;padding:0 24px;">

        <div style="margin-bottom:40px;">
            <div class="fb-eyebrow">Orientation</div>
            <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:8px 0 0;letter-spacing:-.02em;">
                Nous pouvons vous orienter
            </h1>
        </div>

        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:32px;box-shadow:var(--shadow-sm);margin-bottom:20px;">
            <p style="font-size:15px;color:var(--fg-secondary);line-height:1.7;margin:0;">
                Votre situation est particulière ou notre parcours guidé ne couvre pas encore votre besoin.
                Contactez-nous directement — nous vous orientons vers le bon interlocuteur.
            </p>
        </div>

        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <a href="mailto:contact@lafabrique-benfeld.fr" class="fb-btn fb-btn-primary">
                Contactez-nous
            </a>
            @if ($hasHistory)
                <a href="{{ route('parcours.back') }}" class="fb-btn fb-btn-ghost" style="font-size:14px;">← Retour</a>
            @endif
            <a href="{{ route('parcours.start') }}" class="fb-btn fb-btn-ghost" style="font-size:14px;">Recommencer</a>
        </div>

    </div>
</section>

</x-layouts.app>
