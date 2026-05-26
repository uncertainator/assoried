<x-layouts.app title="{{ $service->name }} — La Fabrique">

<section class="fb-section" style="padding-top:64px;padding-bottom:80px;">
    <div style="max-width:640px;margin:0 auto;padding:0 24px;">

        <div style="margin-bottom:40px;">
            <div class="fb-eyebrow">Votre service</div>
            <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:8px 0 0;letter-spacing:-.02em;">
                {{ $service->name }}
            </h1>
            @if ($service->branche)
                <div style="margin-top:8px;">
                    <span style="display:inline-block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:100px;padding:3px 10px;">
                        {{ $service->branche }}
                    </span>
                </div>
            @endif
        </div>

        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:32px;box-shadow:var(--shadow-sm);margin-bottom:20px;">

            {{-- Accroche --}}
            <p style="font-size:1.0625rem;font-weight:500;color:var(--fg-primary);line-height:1.6;margin:0 0 24px;">
                {{ $service->description }}
            </p>

            {{-- Pour qui --}}
            @if ($service->pour_qui)
                <div style="border-top:1px solid var(--border-subtle);padding-top:20px;margin-bottom:20px;">
                    <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:6px;">
                        Pour qui
                    </div>
                    <p style="font-size:14px;color:var(--fg-secondary);line-height:1.65;margin:0;">
                        {{ $service->pour_qui }}
                    </p>
                </div>
            @endif

            {{-- Cas d'usage --}}
            @if ($service->use_cases && count($service->use_cases) > 0)
                <div style="border-top:1px solid var(--border-subtle);padding-top:20px;margin-bottom:20px;">
                    <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:10px;">
                        Situations typiques
                    </div>
                    <ul style="margin:0;padding-left:0;list-style:none;display:flex;flex-direction:column;gap:6px;">
                        @foreach ($service->use_cases as $cas)
                            <li style="font-size:14px;color:var(--fg-secondary);line-height:1.6;display:flex;gap:8px;align-items:flex-start;">
                                <span style="color:var(--brique-400);margin-top:2px;flex-shrink:0;">→</span>
                                <span>{{ $cas }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Ce que ça produit --}}
            @if ($service->ce_que_ca_produit)
                <div style="border-top:1px solid var(--border-subtle);padding-top:20px;margin-bottom:20px;">
                    <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:6px;">
                        Ce que ça produit
                    </div>
                    <p style="font-size:14px;color:var(--fg-secondary);line-height:1.65;margin:0;">
                        {{ $service->ce_que_ca_produit }}
                    </p>
                </div>
            @endif

            {{-- Format --}}
            @if ($service->format)
                <div style="border-top:1px solid var(--border-subtle);padding-top:20px;">
                    <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--fg-tertiary);margin-bottom:6px;">
                        Format
                    </div>
                    <p style="font-size:14px;color:var(--fg-secondary);line-height:1.65;margin:0;">
                        {{ $service->format }}
                    </p>
                </div>
            @endif

        </div>

        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            @if ($service->cta_type === \App\Enums\ParcoursCtaType::Contact)
                <a href="{{ route('parcours.contact-service', ['service' => $service->slug]) }}"
                   class="fb-btn fb-btn-primary">
                    {{ $service->cta_type->label() }}
                </a>
            @else
                <a href="{{ $service->cta_value }}" class="fb-btn fb-btn-primary" target="_blank" rel="noopener">
                    {{ $service->cta_type->label() }}
                </a>
            @endif

            @if ($hasHistory)
                <a href="{{ route('parcours.back') }}" class="fb-btn fb-btn-ghost" style="font-size:14px;">← Retour</a>
            @endif
            <a href="{{ route('parcours.start') }}" class="fb-btn fb-btn-ghost" style="font-size:14px;">Recommencer</a>
        </div>

    </div>
</section>

</x-layouts.app>
