@if(session('submitted') === 'entreprise')
    <div style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:36px;text-align:center;">
        <div style="font-size:2rem;margin-bottom:16px;">✓</div>
        <p style="font-size:var(--text-base);font-weight:600;color:var(--fg-primary);margin:0 0 8px;">Votre demande a bien été reçue.</p>
        <p style="font-size:var(--text-sm);color:var(--fg-secondary);margin:0;">Nous vous contacterons sous 48h pour convenir d'un premier échange.</p>
    </div>
@else
    <form method="POST" action="{{ route('lab.external.entreprise.store') }}"
          style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:36px;">
        @csrf
        <input type="text" name="_pot" style="display:none;" tabindex="-1" autocomplete="off" aria-hidden="true">

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:20px;">
            <div>
                <label for="e_nom_contact" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                    Prénom et nom <span style="color:var(--color-brique-600);">*</span>
                </label>
                <input type="text" id="e_nom_contact" name="nom_contact"
                       value="{{ old('nom_contact') }}"
                       placeholder="Jean Martin"
                       style="width:100%;padding:10px 12px;border:1px solid {{ $errors->entreprise->has('nom_contact') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
                @error('nom_contact', 'entreprise')
                    <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="e_fonction" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                    Fonction <span style="color:var(--color-brique-600);">*</span>
                </label>
                <input type="text" id="e_fonction" name="fonction"
                       value="{{ old('fonction') }}"
                       placeholder="Directrice des opérations"
                       style="width:100%;padding:10px 12px;border:1px solid {{ $errors->entreprise->has('fonction') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
                @error('fonction', 'entreprise')
                    <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="margin-bottom:20px;">
            <label for="e_raison_sociale" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                Entreprise / Structure <span style="color:var(--color-brique-600);">*</span>
            </label>
            <input type="text" id="e_raison_sociale" name="raison_sociale"
                   value="{{ old('raison_sociale') }}"
                   placeholder="Ma Société SAS"
                   style="width:100%;padding:10px 12px;border:1px solid {{ $errors->entreprise->has('raison_sociale') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
            @error('raison_sociale', 'entreprise')
                <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
            @enderror
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:20px;">
            <div>
                <label for="e_email" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                    Email professionnel <span style="color:var(--color-brique-600);">*</span>
                </label>
                <input type="email" id="e_email" name="email"
                       value="{{ old('email') }}"
                       placeholder="jean@masociete.fr"
                       style="width:100%;padding:10px 12px;border:1px solid {{ $errors->entreprise->has('email') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
                @error('email', 'entreprise')
                    <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="e_telephone" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                    Téléphone <span style="color:var(--color-brique-600);">*</span>
                </label>
                <input type="tel" id="e_telephone" name="telephone"
                       value="{{ old('telephone') }}"
                       placeholder="03 88 12 34 56"
                       style="width:100%;padding:10px 12px;border:1px solid {{ $errors->entreprise->has('telephone') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
                @error('telephone', 'entreprise')
                    <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:20px;">
            <div>
                <label for="e_taille_organisation" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                    Taille de l'organisation <span style="font-weight:400;color:var(--fg-tertiary);">(optionnel)</span>
                </label>
                <select id="e_taille_organisation" name="taille_organisation"
                        style="width:100%;padding:10px 12px;border:1px solid {{ $errors->entreprise->has('taille_organisation') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
                    <option value="">— Choisissez —</option>
                    @foreach(['1–10', '11–50', '51–200', '200+'] as $opt)
                        <option value="{{ $opt }}" {{ old('taille_organisation') === $opt ? 'selected' : '' }}>{{ $opt }} personnes</option>
                    @endforeach
                </select>
                @error('taille_organisation', 'entreprise')
                    <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="e_thematique" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                    Thématique <span style="color:var(--color-brique-600);">*</span>
                </label>
                <select id="e_thematique" name="thematique"
                        style="width:100%;padding:10px 12px;border:1px solid {{ $errors->entreprise->has('thematique') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
                    <option value="">— Choisissez —</option>
                    @foreach(['Design Thinking', 'Intelligence Collective', 'Stratégie', 'Gestion de projet', 'Entrepreneuriat', 'Autre'] as $opt)
                        <option value="{{ $opt }}" {{ old('thematique') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                @error('thematique', 'entreprise')
                    <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="margin-bottom:24px;">
            <label for="e_message" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                Décrivez votre besoin <span style="color:var(--color-brique-600);">*</span>
            </label>
            <textarea id="e_message" name="message" rows="6" maxlength="1200"
                      placeholder="Décrivez le contexte de votre entreprise, votre problématique et ce que vous attendez d'un accompagnement Lab…"
                      style="width:100%;padding:10px 12px;border:1px solid {{ $errors->entreprise->has('message') ? 'var(--color-brique-600)' : 'var(--border-default)' }};border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);resize:vertical;box-sizing:border-box;">{{ old('message') }}</textarea>
            @error('message', 'entreprise')
                <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom:28px;padding:16px;background:var(--bg-raised);border-radius:var(--radius-md);border:1px solid {{ $errors->entreprise->has('rgpd_consent') ? 'var(--color-brique-600)' : 'var(--border-subtle)' }};">
            <label style="display:flex;gap:10px;align-items:flex-start;cursor:pointer;">
                <input type="checkbox" name="rgpd_consent" value="1"
                       {{ old('rgpd_consent') ? 'checked' : '' }}
                       style="margin-top:3px;flex-shrink:0;width:16px;height:16px;cursor:pointer;">
                <span style="font-size:var(--text-sm);color:var(--fg-secondary);line-height:1.6;">
                    J'accepte que les données saisies soient utilisées par La Fabrique pour traiter cette demande.
                    Elles ne seront pas partagées avec des tiers.
                    Conformément au RGPD, vous pouvez exercer vos droits en nous contactant. <span style="color:var(--color-brique-600);">*</span>
                </span>
            </label>
            @error('rgpd_consent', 'entreprise')
                <span style="display:block;margin-top:8px;font-size:var(--text-xs);color:var(--color-brique-600);font-weight:500;">{{ $message }}</span>
            @enderror
        </div>

        {{-- Mention tarifaire --}}
        <div style="margin-bottom:20px;padding:14px 16px;background:var(--bg-raised);border-left:3px solid var(--color-ocre-500);border-radius:0 var(--radius-md) var(--radius-md) 0;">
            <p style="font-size:var(--text-sm);color:var(--fg-secondary);margin:0;line-height:1.6;">
                Cette prestation est proposée sur devis. Notre équipe vous contactera pour établir une proposition adaptée.
            </p>
        </div>

        <button type="submit" class="fb-btn fb-btn-primary">Envoyer ma demande</button>
    </form>
@endif
