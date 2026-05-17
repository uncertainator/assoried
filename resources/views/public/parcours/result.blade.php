<x-layouts.app title="{{ $service->name }} — La Fabrique">

<section class="fb-section" style="padding-top:64px;padding-bottom:80px;">
    <div style="max-width:640px;margin:0 auto;padding:0 24px;">

        <div style="margin-bottom:40px;">
            <div class="fb-eyebrow">Votre service</div>
            <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:8px 0 0;letter-spacing:-.02em;">
                {{ $service->name }}
            </h1>
        </div>

        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:32px;box-shadow:var(--shadow-sm);margin-bottom:20px;">
            <p style="font-size:15px;color:var(--fg-secondary);line-height:1.7;margin:0 0 24px;">
                {{ $service->description }}
            </p>

            @if ($service->use_cases)
                <div style="border-top:1px solid var(--border-subtle);padding-top:20px;margin-top:8px;">
                    <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:10px;">
                        Cas d'usage typiques
                    </div>
                    <p style="font-size:14px;color:var(--fg-secondary);line-height:1.65;margin:0;">
                        {{ $service->use_cases }}
                    </p>
                </div>
            @endif
        </div>

        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <a href="{{ $service->cta_value }}" class="fb-btn fb-btn-primary" target="_blank" rel="noopener">
                {{ $service->cta_type->label() }}
            </a>
            @if ($hasHistory)
                <a href="{{ route('parcours.back') }}" class="fb-btn fb-btn-ghost" style="font-size:14px;">← Retour</a>
            @endif
            <a href="{{ route('parcours.start') }}" class="fb-btn fb-btn-ghost" style="font-size:14px;">Recommencer</a>
        </div>

    </div>
</section>

</x-layouts.app>
