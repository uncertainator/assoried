<x-layouts.admin :title="'Saisie terrain — '.$consultation->titre">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Consultation publique — Saisie terrain</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            {{ $consultation->titre }}
        </h1>
    </div>
    <a href="{{ route('admin.consultations.show', $consultation) }}" class="fb-btn fb-btn-ghost fb-btn-sm">← Retour</a>
</div>

<div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:8px;padding:12px 16px;margin-bottom:24px;font-size:14px;color:var(--fg-secondary);">
    Mode : {{ $consultation->mode_recueil->label() }} · Les réponses saisies ici sont enregistrées sans restriction IP (source = terrain).
</div>

@if ($errors->any())
    <div style="background:var(--brique-100);border:1px solid var(--brique-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--brique-700);">
        @foreach ($errors->all() as $error) <div>{{ $error }}</div> @endforeach
    </div>
@endif

<div style="max-width:640px;" x-data="{
    reponses: [
        @if ($consultation->mode_recueil->value === 'signature')
            { prenom: '', nom: '' }
        @elseif ($consultation->mode_recueil->value === 'vote_indicatif')
            { choix: '' }
        @else
            { contenu: '' }
        @endif
    ]
}">
    <form method="POST" action="{{ route('admin.consultations.terrain.store', $consultation) }}">
        @csrf

        <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:16px;">
            <template x-for="(rep, i) in reponses" :key="i">
                <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:8px;padding:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <span style="font-size:13px;font-weight:500;color:var(--fg-tertiary);">Réponse <span x-text="i+1"></span></span>
                        <button type="button" @click="reponses.splice(i,1)" x-show="reponses.length > 1"
                            style="color:var(--brique-500);background:none;border:none;cursor:pointer;font-size:18px;">×</button>
                    </div>

                    @if ($consultation->mode_recueil->value === 'avis_libre')
                        <textarea :name="`reponses[${i}][contenu]`" x-model="rep.contenu" rows="3" maxlength="500"
                            style="width:100%;border:1px solid var(--border-default);border-radius:6px;padding:8px 12px;font-size:14px;resize:vertical;font-family:inherit;"
                            placeholder="Avis..."></textarea>

                    @elseif ($consultation->mode_recueil->value === 'signature')
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                            <input type="text" :name="`reponses[${i}][prenom]`" x-model="rep.prenom"
                                style="border:1px solid var(--border-default);border-radius:6px;padding:8px 12px;font-size:14px;"
                                placeholder="Prénom">
                            <input type="text" :name="`reponses[${i}][nom]`" x-model="rep.nom"
                                style="border:1px solid var(--border-default);border-radius:6px;padding:8px 12px;font-size:14px;"
                                placeholder="Nom">
                        </div>

                    @elseif ($consultation->mode_recueil->value === 'vote_indicatif')
                        <select :name="`reponses[${i}][choix]`" x-model="rep.choix"
                            style="width:100%;border:1px solid var(--border-default);border-radius:6px;padding:8px 12px;font-size:14px;">
                            <option value="">-- Sélectionner --</option>
                            @foreach ($consultation->options ?? [] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </template>
        </div>

        <button type="button"
            @click="reponses.push(@if ($consultation->mode_recueil->value === 'signature') { prenom: '', nom: '' } @elseif ($consultation->mode_recueil->value === 'vote_indicatif') { choix: '' } @else { contenu: '' } @endif)"
            style="width:100%;border:1px dashed var(--border-default);border-radius:8px;padding:10px;font-size:14px;color:var(--fg-secondary);background:none;cursor:pointer;margin-bottom:20px;">
            + Ajouter une réponse
        </button>

        <div style="display:flex;gap:12px;">
            <button type="submit" class="fb-btn fb-btn-primary">Enregistrer les réponses terrain</button>
            <a href="{{ route('admin.consultations.show', $consultation) }}" class="fb-btn fb-btn-ghost">Annuler</a>
        </div>
    </form>
</div>

</x-layouts.admin>
