@if(session('submitted') === 'citoyen')
    <div style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:36px;text-align:center;">
        <div style="font-size:2rem;margin-bottom:16px;">✓</div>
        <p style="font-size:var(--text-base);font-weight:600;color:var(--fg-primary);margin:0 0 8px;">Votre demande a bien été reçue.</p>
        <p style="font-size:var(--text-sm);color:var(--fg-secondary);margin:0;">Nous reviendrons vers vous sous 5 jours ouvrés.</p>
    </div>
@else
    <form method="POST" action="{{ route('lab.external.citoyen.store') }}"
          style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:36px;">
        @csrf
        <input type="text" name="_pot" style="display:none;" tabindex="-1" autocomplete="off" aria-hidden="true">

        <div style="margin-bottom:20px;">
            <label for="c_nom_contact" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                Prénom et nom <span style="color:var(--color-brique-600);">*</span>
            </label>
            <input type="text" id="c_nom_contact" name="nom_contact"
                   value="{{ old('nom_contact') }}"
                   placeholder="Marie Dupont"
                   style="width:100%;padding:10px 12px;border:1px solid {{ $errors->citoyen->has('nom_contact') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
            @error('nom_contact', 'citoyen')
                <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
            @enderror
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:20px;">
            <div>
                <label for="c_email" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                    Email <span style="color:var(--color-brique-600);">*</span>
                </label>
                <input type="email" id="c_email" name="email"
                       value="{{ old('email') }}"
                       placeholder="marie@exemple.fr"
                       style="width:100%;padding:10px 12px;border:1px solid {{ $errors->citoyen->has('email') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
                @error('email', 'citoyen')
                    <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="c_telephone" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                    Téléphone <span style="font-weight:400;color:var(--fg-tertiary);">(optionnel)</span>
                </label>
                <input type="tel" id="c_telephone" name="telephone"
                       value="{{ old('telephone') }}"
                       placeholder="06 12 34 56 78"
                       style="width:100%;padding:10px 12px;border:1px solid {{ $errors->citoyen->has('telephone') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
                @error('telephone', 'citoyen')
                    <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="margin-bottom:20px;">
            <label for="c_type_projet" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                Type de projet <span style="color:var(--color-brique-600);">*</span>
            </label>
            <select id="c_type_projet" name="type_projet"
                    style="width:100%;padding:10px 12px;border:1px solid {{ $errors->citoyen->has('type_projet') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
                <option value="">— Choisissez —</option>
                @foreach(['Initiative citoyenne', 'Projet associatif', 'Projet personnel', 'Autre'] as $opt)
                    <option value="{{ $opt }}" {{ old('type_projet') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
            @error('type_projet', 'citoyen')
                <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom:24px;">
            <label for="c_message" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                Décrivez votre projet en quelques lignes <span style="color:var(--color-brique-600);">*</span>
            </label>
            <textarea id="c_message" name="message" rows="5" maxlength="800"
                      placeholder="Quel est votre projet ? À quel stade en êtes-vous ? Quel type d'accompagnement recherchez-vous ?"
                      style="width:100%;padding:10px 12px;border:1px solid {{ $errors->citoyen->has('message') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);resize:vertical;box-sizing:border-box;">{{ old('message') }}</textarea>
            @error('message', 'citoyen')
                <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom:28px;padding:16px;background:var(--bg-raised);border-radius:var(--radius-md);border:1px solid {{ $errors->citoyen->has('rgpd_consent') ? 'var(--color-brique-600)' : 'var(--border-subtle)' }};">
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
            @error('rgpd_consent', 'citoyen')
                <span style="display:block;margin-top:8px;font-size:var(--text-xs);color:var(--color-brique-600);font-weight:500;">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="fb-btn fb-btn-primary">Envoyer ma demande</button>
    </form>
@endif
