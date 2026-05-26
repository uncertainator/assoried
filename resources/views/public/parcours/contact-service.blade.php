<x-layouts.app title="Nous contacter — La Fabrique">

<section class="fb-section" style="padding-top:64px;padding-bottom:80px;">
    <div style="max-width:640px;margin:0 auto;padding:0 24px;">

        <div style="margin-bottom:40px;">
            <div class="fb-eyebrow">Contact</div>
            <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:8px 0 0;letter-spacing:-.02em;">
                Parlons de votre projet
            </h1>
            @if ($serviceName)
                <p style="font-size:15px;color:var(--fg-secondary);margin:12px 0 0;line-height:1.6;">
                    Vous nous contactez au sujet du service <strong>{{ $serviceName }}</strong>.
                </p>
            @endif
        </div>

        @if (session('success'))
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:var(--radius-md);padding:16px;margin-bottom:24px;color:#166534;font-size:14px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:32px;box-shadow:var(--shadow-sm);">
            <form method="POST" action="{{ route('parcours.contact-service.send') }}">
                @csrf

                {{-- Champ caché : slug du service sélectionné --}}
                <input type="hidden" name="service_slug" value="{{ $serviceSlug }}">

                @if ($errors->any())
                    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:var(--radius-md);padding:12px 16px;margin-bottom:20px;color:#dc2626;font-size:14px;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="fb-field">
                    <label class="fb-label" for="name">Votre nom</label>
                    <input type="text" id="name" name="name" class="fb-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name') }}" required maxlength="100" placeholder="Prénom Nom">
                    @error('name')<span class="fb-error">{{ $message }}</span>@enderror
                </div>

                <div class="fb-field" style="margin-top:16px;">
                    <label class="fb-label" for="email">Votre email</label>
                    <input type="email" id="email" name="email" class="fb-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email') }}" required maxlength="150" placeholder="vous@exemple.fr">
                    @error('email')<span class="fb-error">{{ $message }}</span>@enderror
                </div>

                <div class="fb-field" style="margin-top:16px;">
                    <label class="fb-label" for="message">Votre message</label>
                    <textarea id="message" name="message" rows="5"
                              class="fb-textarea {{ $errors->has('message') ? 'is-invalid' : '' }}"
                              required maxlength="2000"
                              placeholder="Décrivez votre situation, votre projet, vos questions…">{{ old('message') }}</textarea>
                    @error('message')<span class="fb-error">{{ $message }}</span>@enderror
                </div>

                <div style="display:flex;gap:10px;margin-top:28px;align-items:center;">
                    <button type="submit" class="fb-btn fb-btn-primary">Envoyer →</button>
                    @if ($hasHistory)
                        <a href="{{ route('parcours.back') }}" class="fb-btn fb-btn-ghost" style="font-size:14px;">← Retour</a>
                    @endif
                </div>
            </form>
        </div>

        <p style="font-size:13px;color:var(--fg-tertiary);margin-top:16px;line-height:1.6;">
            Vous pouvez aussi nous écrire directement à
            <a href="mailto:contact@lafabrique-benfeld.fr" style="color:inherit;">contact@lafabrique-benfeld.fr</a>.
        </p>

    </div>
</section>

</x-layouts.app>
