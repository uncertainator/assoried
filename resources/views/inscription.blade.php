<x-layouts.app title="Adhérer — La Fabrique">

<section style="max-width:720px;margin:64px auto;padding:0 24px;">
    <div class="fb-eyebrow">Inscription</div>
    <h1 class="fb-h1">Rejoignez<br><em class="fb-italic-accent">La Fabrique</em></h1>
    <p class="fb-lead" style="margin-bottom:40px;">
        Entrez votre adresse email et choisissez les cercles qui vous intéressent.
    </p>

    @if ($errors->any())
        <div class="flash-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('inscription.store') }}" x-data="{ authMethod: '{{ old('auth_method', 'magic_link') }}' }">
        @csrf

        <div class="fb-field">
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" class="fb-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                   placeholder="votre@email.fr" value="{{ old('email') }}" required autocomplete="email">
            @error('email')<span class="fb-error">{{ $message }}</span>@enderror
        </div>

        {{-- Choix de la méthode de connexion --}}
        <div style="margin-bottom:24px;">
            <div style="font:500 12px var(--font-sans);color:var(--fg-primary);letter-spacing:.02em;margin-bottom:10px;">
                Comment souhaitez-vous vous connecter ?
            </div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <label style="display:flex;align-items:flex-start;gap:10px;padding:14px;border:1px solid var(--border-subtle);border-radius:var(--radius-md);cursor:pointer;background:var(--bg-surface-2);transition:border-color .12s;"
                       :style="authMethod === 'magic_link' ? 'border-color:var(--brique-400);' : ''">
                    <input type="radio" name="auth_method" value="magic_link"
                           x-model="authMethod"
                           style="margin-top:3px;accent-color:var(--brique-500);flex-shrink:0;">
                    <div>
                        <div style="font:600 14px var(--font-sans);color:var(--fg-primary);">Recevoir un lien magique par email</div>
                        <div style="font-size:12px;color:var(--fg-tertiary);margin-top:2px;">Un lien de connexion vous est envoyé à chaque fois — aucun mot de passe à retenir.</div>
                    </div>
                </label>
                <label style="display:flex;align-items:flex-start;gap:10px;padding:14px;border:1px solid var(--border-subtle);border-radius:var(--radius-md);cursor:pointer;background:var(--bg-surface-2);transition:border-color .12s;"
                       :style="authMethod === 'password' ? 'border-color:var(--brique-400);' : ''">
                    <input type="radio" name="auth_method" value="password"
                           x-model="authMethod"
                           style="margin-top:3px;accent-color:var(--brique-500);flex-shrink:0;">
                    <div>
                        <div style="font:600 14px var(--font-sans);color:var(--fg-primary);">Créer un mot de passe</div>
                        <div style="font-size:12px;color:var(--fg-tertiary);margin-top:2px;">Vous vous connecterez avec votre email et mot de passe.</div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Champs mot de passe (conditionnels) --}}
        <div x-show="authMethod === 'password'" x-cloak style="margin-bottom:24px;">
            <div class="fb-field">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password"
                       class="fb-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                       autocomplete="new-password"
                       placeholder="8 caractères minimum">
                @error('password')<span class="fb-error">{{ $message }}</span>@enderror
            </div>
            <div class="fb-field">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="fb-input"
                       autocomplete="new-password"
                       placeholder="Retapez votre mot de passe">
            </div>
        </div>

        @if ($circles->count())
            <div style="margin-bottom:24px;">
                <div style="font:500 12px var(--font-sans);color:var(--fg-primary);letter-spacing:.02em;margin-bottom:12px;">
                    Cercles thématiques (facultatif — vous pourrez les modifier plus tard)
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;">
                    @foreach ($circles as $circle)
                        <label style="display:flex;align-items:flex-start;gap:10px;padding:14px;border:1px solid var(--border-subtle);border-radius:var(--radius-md);cursor:pointer;background:var(--bg-surface-2);transition:border-color .12s;">
                            <input type="checkbox" name="circles[]" value="{{ $circle->id }}"
                                   {{ in_array($circle->id, old('circles', [])) ? 'checked' : '' }}
                                   style="margin-top:2px;accent-color:var(--brique-500);flex-shrink:0;">
                            <div>
                                <div style="font:600 14px var(--font-sans);color:var(--fg-primary);">{{ $circle->name }}</div>
                                @if ($circle->description)
                                    <div style="font-size:12px;color:var(--fg-tertiary);margin-top:2px;">{{ Str::limit($circle->description, 70) }}</div>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <button type="submit" class="fb-btn fb-btn-primary fb-btn-lg">
                <span x-show="authMethod === 'magic_link'">Recevoir mon lien →</span>
                <span x-show="authMethod === 'password'" x-cloak>Créer mon compte →</span>
            </button>
            <a href="{{ route('home') }}" class="fb-link-quiet">Annuler</a>
        </div>

        <p style="font-size:12px;color:var(--fg-tertiary);margin-top:20px;line-height:1.6;">
            En vous inscrivant, vous acceptez notre
            <a href="{{ route('pages.show', ['slug' => 'politique-de-confidentialite']) }}" style="color:var(--brique-600);">politique de confidentialité</a>.
            Vos données ne sont partagées avec personne.
        </p>
    </form>
</section>

</x-layouts.app>
