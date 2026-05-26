<x-layouts.app title="Lab La Fabrique — Déposez votre demande">

<div style="max-width:760px;margin:0 auto;padding:48px 24px;">

    {{-- En-tête page --}}
    <div style="margin-bottom:56px;">
        <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:6px;">Lab La Fabrique</div>
        <h1 style="font-family:var(--font-display);font-size:2.5rem;font-weight:700;color:var(--fg-primary);margin:0 0 16px;letter-spacing:-.02em;">Déposez votre demande</h1>
        <p style="font-size:var(--text-base);color:var(--fg-secondary);line-height:1.7;max-width:60ch;">
            Le Lab accompagne citoyens, associations et entreprises du territoire. Choisissez le formulaire qui correspond à votre situation.
        </p>
    </div>

    {{-- =====================================================
         Section citoyen / personnel
         ===================================================== --}}
    <section id="form-citoyen" style="margin-bottom:72px;">

        <div style="margin-bottom:28px;">
            <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:6px;">Programme citoyen</div>
            <h2 style="font-family:var(--font-display);font-size:1.75rem;font-weight:700;color:var(--fg-primary);margin:0 0 12px;letter-spacing:-.02em;">Je monte un projet,<br>j'entreprends</h2>
            <p style="font-size:var(--text-base);color:var(--fg-secondary);line-height:1.7;max-width:60ch;">
                Vous avez une idée, un projet de territoire ou une envie d'entreprendre ?
                Le Lab vous accompagne : ateliers de facilitation, mise en réseau, accès à des ressources et à une communauté engagée.
                C'est gratuit.
            </p>
        </div>

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
    </section>

    <hr style="border:none;border-top:1px solid var(--border-subtle);margin-bottom:72px;">

    {{-- =====================================================
         Section entreprise
         ===================================================== --}}
    <section id="form-entreprise">

        <div style="margin-bottom:28px;">
            <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:6px;">Programme entreprise</div>
            <h2 style="font-family:var(--font-display);font-size:1.75rem;font-weight:700;color:var(--fg-primary);margin:0 0 12px;letter-spacing:-.02em;">Mon entreprise<br>fait appel au Lab</h2>
            <p style="font-size:var(--text-base);color:var(--fg-secondary);line-height:1.7;max-width:60ch;">
                Le Lab accompagne les entreprises du territoire qui souhaitent innover, faciliter leur organisation ou
                explorer de nouvelles approches collaboratives. Déposez votre demande et un référent Lab vous recontactera.
            </p>
        </div>

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
    </section>

</div>

</x-layouts.app>
