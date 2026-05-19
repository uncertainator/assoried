<x-layouts.app :title="$event->title . ' — La Fabrique'">

<section class="fb-section" style="padding-top:64px;">
    <div style="max-width:800px;margin:0 auto;padding:0 24px;">

        {{-- Retour --}}
        <a href="{{ route('evenements') }}" style="font-size:13px;color:var(--fg-tertiary);text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-bottom:32px;">
            ← Voir tous les événements
        </a>

        {{-- Flash messages --}}
        @if (session('success'))
            <div style="background:var(--mousse-100);border:1px solid var(--mousse-300);color:var(--mousse-700);padding:14px 18px;border-radius:var(--radius-md);margin-bottom:24px;font-size:14px;">
                {{ session('success') }}
            </div>
        @endif
        @if (session('info'))
            <div style="background:var(--ocre-50);border:1px solid var(--ocre-200);color:var(--ocre-700);padding:14px 18px;border-radius:var(--radius-md);margin-bottom:24px;font-size:14px;">
                {{ session('info') }}
            </div>
        @endif

        {{-- En-tête événement --}}
        @php
            $color = match($event->tag) {
                'Atelier'     => ['border' => 'var(--brique-500)', 'tag' => 'var(--brique-600)'],
                'Information' => ['border' => 'var(--ocre-400)',   'tag' => 'var(--ocre-600)'],
                default       => ['border' => 'var(--mousse-500)', 'tag' => 'var(--mousse-500)'],
            };
        @endphp

        <article style="border-top:4px solid {{ $color['border'] }};background:var(--bg-surface-2);border-radius:var(--radius-lg);padding:32px;margin-bottom:32px;">
            @if ($event->tag)
                <div style="font-size:11px;font-weight:700;letter-spacing:.08em;color:{{ $color['tag'] }};text-transform:uppercase;margin-bottom:8px;">
                    {{ $event->tag }}
                </div>
            @endif

            <h1 style="font-size:28px;font-weight:700;color:var(--fg-primary);margin:0 0 16px;line-height:1.25;">
                {{ $event->title }}
            </h1>

            <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;">
                <div style="font-size:14px;color:var(--fg-secondary);display:flex;align-items:center;gap:8px;">
                    <span style="color:var(--fg-tertiary);">📅</span>
                    <span class="fb-mono">{{ $event->starts_at->isoFormat('dddd D MMMM YYYY [à] HH:mm') }}</span>
                    @if ($event->ends_at)
                        <span style="color:var(--fg-tertiary);">→ {{ $event->ends_at->isoFormat('HH:mm') }}</span>
                    @endif
                </div>
                @if ($event->location)
                    <div style="font-size:14px;color:var(--fg-secondary);display:flex;align-items:center;gap:8px;">
                        <span style="color:var(--fg-tertiary);">📍</span>
                        <span>{{ $event->location }}</span>
                    </div>
                @endif
                <div style="font-size:14px;color:var(--fg-tertiary);display:flex;align-items:center;gap:8px;">
                    <span>Organisé par :</span>
                    <span style="color:var(--fg-secondary);font-weight:500;">{{ $event->circle->name }}</span>
                </div>
            </div>

            @if ($event->description)
                <p style="font-size:15px;color:var(--fg-secondary);line-height:1.65;margin:0 0 20px;">
                    {{ $event->description }}
                </p>
            @endif

            @if ($event->foot_type)
                <div>
                    @if ($event->foot_type === 'places_limitees')
                        <span class="fb-badge fb-badge-mousse">Places limitées</span>
                    @else
                        <span class="fb-badge fb-badge-ocre">Entrée libre</span>
                    @endif
                </div>
            @endif
        </article>

        {{-- Section inscription --}}
        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:32px;">
            <h2 style="font-size:18px;font-weight:600;color:var(--fg-primary);margin:0 0 20px;">
                S'inscrire à cet événement
            </h2>

            @if ($alreadyRegistered)
                {{-- Déjà inscrit --}}
                <div style="display:flex;align-items:center;gap:12px;padding:18px;background:var(--mousse-50);border:1px solid var(--mousse-200);border-radius:var(--radius-md);">
                    <span style="font-size:20px;">✓</span>
                    <div>
                        <div style="font-size:15px;font-weight:600;color:var(--mousse-700);">Vous êtes inscrit</div>
                        <div style="font-size:13px;color:var(--mousse-600);margin-top:2px;">Votre présence est enregistrée pour cet événement.</div>
                    </div>
                </div>

            @elseif (auth()->check())
                {{-- Adhérent connecté — 1 clic --}}
                <p style="font-size:14px;color:var(--fg-secondary);margin:0 0 20px;">
                    Vous êtes connecté en tant que <strong>{{ auth()->user()->email }}</strong>.
                </p>
                <form method="POST" action="{{ route('evenements.register', $event) }}">
                    @csrf
                    <button type="submit" class="fb-btn fb-btn-primary">
                        Confirmer ma présence →
                    </button>
                </form>

            @else
                {{-- Visiteur — formulaire simple --}}
                @if ($errors->any())
                    <div style="background:var(--brique-50);border:1px solid var(--brique-200);color:var(--brique-700);padding:14px 18px;border-radius:var(--radius-md);margin-bottom:20px;font-size:14px;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('evenements.register', $event) }}">
                    @csrf

                    <div class="fb-field" style="margin-bottom:16px;">
                        <label for="name" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">
                            Prénom et nom <span style="color:var(--brique-500);">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            maxlength="150"
                            required
                            class="fb-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            placeholder="Ex. Marie Dupont"
                        >
                    </div>

                    <div class="fb-field" style="margin-bottom:24px;">
                        <label for="email" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">
                            Adresse email <span style="color:var(--brique-500);">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            maxlength="200"
                            required
                            class="fb-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            placeholder="votre@email.fr"
                        >
                    </div>

                    <button type="submit" class="fb-btn fb-btn-primary">
                        Je m'inscris →
                    </button>
                </form>

                <p style="font-size:12px;color:var(--fg-tertiary);margin-top:16px;">
                    Déjà membre ?
                    <a href="{{ route('login') }}" style="color:var(--brique-500);text-decoration:none;">Connectez-vous</a>
                    pour une inscription en 1 clic.
                </p>
            @endif
        </div>

    </div>
</section>

</x-layouts.app>
