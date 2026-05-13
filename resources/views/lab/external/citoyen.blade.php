<x-layouts.app title="Programme citoyen — Lab La Fabrique">

<div style="max-width:720px;margin:0 auto;padding:48px 24px;">

    {{-- En-tête --}}
    <div style="margin-bottom:40px;">
        <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:6px;">Lab La Fabrique · Programme citoyen</div>
        <h1 style="font-family:var(--font-display);font-size:2.25rem;font-weight:700;color:var(--fg-primary);margin:0 0 16px;letter-spacing:-.02em;">Je monte un projet,<br>j'entreprends</h1>
        <p style="font-size:var(--text-base);color:var(--fg-secondary);line-height:1.7;max-width:60ch;">
            Vous avez une idée, un projet de territoire ou une envie d'entreprendre ?
            Le Lab vous accompagne : ateliers de facilitation, mise en réseau, accès à des ressources et à une communauté engagée.
            Décrivez votre projet ci-dessous et un référent Lab vous recontactera.
        </p>
    </div>

    {{-- Formulaire --}}
    <form method="POST" action="{{ route('lab.external.citoyen.store') }}"
          style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:36px;">
        @csrf

        {{-- Honeypot --}}
        <input type="text" name="_pot" style="display:none;" tabindex="-1" autocomplete="off" aria-hidden="true">

        <div style="margin-bottom:20px;">
            <label for="nom_contact" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                Prénom et nom <span style="color:var(--color-brique-600);">*</span>
            </label>
            <input type="text" id="nom_contact" name="nom_contact"
                   value="{{ old('nom_contact') }}"
                   placeholder="Marie Dupont"
                   style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('nom_contact') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
            @error('nom_contact')
                <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
            @enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
            <div>
                <label for="email" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                    Email <span style="color:var(--color-brique-600);">*</span>
                </label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="marie@exemple.fr"
                       style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('email') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
                @error('email')
                    <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="telephone" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                    Téléphone <span style="font-weight:400;color:var(--fg-tertiary);">(optionnel)</span>
                </label>
                <input type="tel" id="telephone" name="telephone"
                       value="{{ old('telephone') }}"
                       placeholder="06 12 34 56 78"
                       style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('telephone') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
                @error('telephone')
                    <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="margin-bottom:20px;">
            <label for="territoire" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                Commune ou territoire concerné <span style="font-weight:400;color:var(--fg-tertiary);">(optionnel)</span>
            </label>
            <input type="text" id="territoire" name="territoire"
                   value="{{ old('territoire') }}"
                   placeholder="Ried, Sélestat, Bas-Rhin…"
                   style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('territoire') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
            @error('territoire')
                <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom:24px;">
            <label for="message" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                Décrivez votre projet ou besoin <span style="color:var(--color-brique-600);">*</span>
            </label>
            <textarea id="message" name="message" rows="6"
                      placeholder="Quel est votre projet ? À quel stade en êtes-vous ? Quel type d'accompagnement recherchez-vous ?"
                      style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('message') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);resize:vertical;box-sizing:border-box;">{{ old('message') }}</textarea>
            @error('message')
                <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
            @enderror
        </div>

        {{-- Consentement RGPD --}}
        <div style="margin-bottom:28px;padding:16px;background:var(--bg-raised);border-radius:var(--radius-md);border:1px solid {{ $errors->has('rgpd_consent') ? 'var(--color-brique-600)' : 'var(--border-subtle)' }};">
            <label style="display:flex;gap:10px;align-items:flex-start;cursor:pointer;">
                <input type="checkbox" name="rgpd_consent" value="1"
                       {{ old('rgpd_consent') ? 'checked' : '' }}
                       style="margin-top:3px;flex-shrink:0;width:16px;height:16px;cursor:pointer;">
                <span style="font-size:var(--text-sm);color:var(--fg-secondary);line-height:1.6;">
                    J'accepte que mes données personnelles soient utilisées par La Fabrique pour traiter ma demande.
                    Elles ne seront pas partagées avec des tiers.
                    Conformément au RGPD, vous pouvez exercer vos droits en nous contactant. <span style="color:var(--color-brique-600);">*</span>
                </span>
            </label>
            @error('rgpd_consent')
                <span style="display:block;margin-top:8px;font-size:var(--text-xs);color:var(--color-brique-600);font-weight:500;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display:flex;gap:12px;align-items:center;">
            <button type="submit" class="fb-btn fb-btn-primary">Envoyer ma demande</button>
            <a href="{{ route('home') }}" class="fb-btn fb-btn-ghost">Annuler</a>
        </div>
    </form>
</div>

</x-layouts.app>
