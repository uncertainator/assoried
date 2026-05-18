<x-layouts.app :title="$consultation->titre">

<section class="fb-section" style="padding-top:64px;padding-bottom:64px;">
    <div style="max-width:760px;margin:0 auto;padding:0 24px;">

        <div class="fb-eyebrow" style="margin-bottom:8px;">Consultation publique</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:0 0 16px;letter-spacing:-.02em;">
            {{ $consultation->titre }}
        </h1>

        @if ($consultation->date_cloture)
            <p style="font-size:14px;color:var(--fg-tertiary);margin-bottom:16px;">
                @if ($consultation->estCloturee())
                    Consultation clôturée le {{ $consultation->date_cloture->translatedFormat('j M Y') }}.
                @else
                    Ouverte jusqu'au {{ $consultation->date_cloture->translatedFormat('j M Y à H\hi') }}.
                @endif
            </p>
        @endif

        @if ($consultation->description)
            <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:10px;padding:20px;margin-bottom:28px;font-size:15px;line-height:1.7;color:var(--fg-secondary);">
                {{ $consultation->description }}
            </div>
        @endif

        @if (session('success'))
            <div style="background:var(--mousse-100);border:1px solid var(--mousse-300);border-radius:8px;padding:14px 18px;margin-bottom:24px;font-size:14px;color:var(--mousse-700);">
                {{ session('success') }}
            </div>
        @endif

        @if ($consultation->estCloturee())
            <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:8px;padding:16px 20px;margin-bottom:24px;font-size:14px;color:var(--fg-tertiary);">
                Cette consultation est clôturée. Vous pouvez consulter les résultats ci-dessous.
            </div>
            <a href="{{ route('consultations.resultats', $consultation) }}" class="fb-btn fb-btn-primary">
                Voir les résultats
            </a>
        @else
            <form method="POST" action="{{ route('consultations.soumettre', $consultation) }}">
                @csrf

                @if ($errors->any())
                    <div style="background:var(--brique-100);border:1px solid var(--brique-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--brique-700);">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if ($consultation->mode_recueil->value === 'avis_libre')
                    <div x-data="{ count: {{ strlen(old('contenu', '')) }} }" style="margin-bottom:20px;">
                        <label style="display:block;font-size:14px;font-weight:500;margin-bottom:8px;">
                            Votre avis <span style="color:var(--fg-tertiary);">(500 caractères max)</span>
                        </label>
                        <textarea
                            name="contenu"
                            maxlength="500"
                            rows="5"
                            x-on:input="count = $event.target.value.length"
                            style="width:100%;border:1px solid var(--border-default);border-radius:8px;padding:12px;font-size:15px;resize:vertical;font-family:inherit;"
                            placeholder="Exprimez votre avis..."
                        >{{ old('contenu') }}</textarea>
                        <div style="text-align:right;font-size:12px;color:var(--fg-tertiary);margin-top:4px;">
                            <span x-text="count"></span>/500
                        </div>
                        @error('contenu')
                            <p style="color:var(--brique-600);font-size:13px;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                @elseif ($consultation->mode_recueil->value === 'signature')
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Prénom</label>
                            <input type="text" name="prenom" value="{{ old('prenom') }}"
                                style="width:100%;border:1px solid var(--border-default);border-radius:8px;padding:10px 12px;font-size:15px;"
                                placeholder="Marie">
                            @error('prenom')
                                <p style="color:var(--brique-600);font-size:13px;margin-top:4px;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Nom</label>
                            <input type="text" name="nom" value="{{ old('nom') }}"
                                style="width:100%;border:1px solid var(--border-default);border-radius:8px;padding:10px 12px;font-size:15px;"
                                placeholder="Dupont">
                            @error('nom')
                                <p style="color:var(--brique-600);font-size:13px;margin-top:4px;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                @elseif ($consultation->mode_recueil->value === 'vote_indicatif')
                    <fieldset style="border:none;padding:0;margin:0 0 20px;">
                        <legend style="font-size:14px;font-weight:500;margin-bottom:12px;">Choisissez une option</legend>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            @foreach ($consultation->options ?? [] as $option)
                                <label style="display:flex;align-items:center;gap:10px;padding:12px 16px;border:1px solid var(--border-default);border-radius:8px;cursor:pointer;">
                                    <input type="radio" name="choix" value="{{ $option }}"
                                        {{ old('choix') === $option ? 'checked' : '' }}
                                        style="width:18px;height:18px;accent-color:var(--brique-500);">
                                    <span style="font-size:15px;">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('choix')
                            <p style="color:var(--brique-600);font-size:13px;margin-top:8px;">{{ $message }}</p>
                        @enderror
                    </fieldset>
                @endif

                <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                    <button type="submit" class="fb-btn fb-btn-primary">
                        Envoyer ma réponse
                    </button>
                    <a href="{{ route('consultations.resultats', $consultation) }}" style="font-size:14px;color:var(--fg-tertiary);text-decoration:none;">
                        Voir les résultats →
                    </a>
                </div>
            </form>
        @endif

    </div>
</section>

</x-layouts.app>
