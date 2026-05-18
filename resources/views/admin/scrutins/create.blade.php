<x-layouts.admin title="Nouveau scrutin — Admin">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Scrutins</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Nouveau scrutin
        </h1>
    </div>
    <a href="{{ route('admin.scrutins.index') }}" class="fb-btn fb-btn-ghost fb-btn-sm">← Retour</a>
</div>

@if ($errors->any())
    <div style="background:var(--brique-100);border:1px solid var(--brique-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--brique-700);">
        <ul style="margin:0;padding-left:16px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.scrutins.store') }}"
      x-data="{
          majorityType: '{{ old('majority_type', 'simple') }}',
          options: {{ json_encode(old('options', [['label'=>'','position'=>1],['label'=>'','position'=>2]])) }},
          addOption() { this.options.push({ label: '', position: this.options.length + 1 }); },
          removeOption(i) { if (this.options.length > 2) { this.options.splice(i, 1); this.options.forEach((o,idx) => o.position = idx + 1); } }
      }">
    @csrf

    <div style="display:flex;flex-direction:column;gap:20px;max-width:640px;">

        {{-- Titre --}}
        <div>
            <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Titre <span style="color:var(--brique-500);">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required maxlength="200"
                   class="fb-input" style="width:100%;" placeholder="Titre du scrutin">
        </div>

        {{-- Description --}}
        <div>
            <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Description</label>
            <textarea name="description" rows="3" class="fb-input" style="width:100%;resize:vertical;"
                      placeholder="Contexte et objectif du scrutin">{{ old('description') }}</textarea>
        </div>

        {{-- Dates --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div>
                <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Date d'ouverture <span style="color:var(--brique-500);">*</span></label>
                <input type="datetime-local" name="opened_at" value="{{ old('opened_at') }}" required class="fb-input" style="width:100%;">
            </div>
            <div>
                <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Date de clôture <span style="color:var(--brique-500);">*</span></label>
                <input type="datetime-local" name="closes_at" value="{{ old('closes_at') }}" required class="fb-input" style="width:100%;">
            </div>
        </div>

        {{-- Quorum --}}
        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:10px;padding:20px;">
            <div style="font-size:14px;font-weight:600;margin-bottom:16px;">Quorum</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Type <span style="color:var(--brique-500);">*</span></label>
                    <select name="quorum_type" class="fb-input" style="width:100%;">
                        <option value="fixed" {{ old('quorum_type','fixed') === 'fixed' ? 'selected' : '' }}>Fixe (nombre de membres)</option>
                        <option value="proportional" {{ old('quorum_type') === 'proportional' ? 'selected' : '' }}>Proportionnel (% membres actifs)</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Valeur <span style="color:var(--brique-500);">*</span></label>
                    <input type="number" name="quorum_value" value="{{ old('quorum_value', 5) }}" required min="0" step="0.01"
                           class="fb-input" style="width:100%;" placeholder="Ex : 5 ou 50">
                    <div style="font-size:12px;color:var(--fg-tertiary);margin-top:4px;">Entier pour fixe, pourcentage pour proportionnel</div>
                </div>
            </div>
        </div>

        {{-- Majorité --}}
        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:10px;padding:20px;">
            <div style="font-size:14px;font-weight:600;margin-bottom:16px;">Majorité</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Type <span style="color:var(--brique-500);">*</span></label>
                    <select name="majority_type" x-model="majorityType" class="fb-input" style="width:100%;">
                        <option value="simple">Majorité simple (> 50 %)</option>
                        <option value="qualified">Majorité qualifiée (seuil configurable)</option>
                    </select>
                </div>
                <div x-show="majorityType === 'qualified'" x-transition>
                    <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Seuil (%) <span style="color:var(--brique-500);">*</span></label>
                    <input type="number" name="majority_threshold" value="{{ old('majority_threshold', 66.67) }}"
                           min="0" max="100" step="0.01" class="fb-input" style="width:100%;" placeholder="Ex : 66.67">
                </div>
            </div>
        </div>

        {{-- Options de vote --}}
        <div>
            <div style="font-size:14px;font-weight:600;margin-bottom:12px;">Options de vote <span style="color:var(--brique-500);">*</span> <span style="font-size:12px;font-weight:400;color:var(--fg-tertiary);">(minimum 2)</span></div>

            <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px;">
                <template x-for="(opt, i) in options" :key="i">
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="text" :name="'options['+i+'][label]'" x-model="opt.label"
                               required maxlength="200" class="fb-input" style="flex:1;" placeholder="Libellé de l'option">
                        <input type="hidden" :name="'options['+i+'][position]'" x-model="opt.position">
                        <button type="button" @click="removeOption(i)"
                                :disabled="options.length <= 2"
                                style="padding:6px 10px;border:1px solid var(--border-subtle);border-radius:6px;background:none;cursor:pointer;color:var(--brique-500);font-size:18px;line-height:1;"
                                :style="options.length <= 2 ? 'opacity:.35;cursor:default;' : ''">×</button>
                    </div>
                </template>
            </div>

            <button type="button" @click="addOption()" class="fb-btn fb-btn-outline fb-btn-sm">
                + Ajouter une option
            </button>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:12px;padding-top:8px;">
            <button type="submit" class="fb-btn fb-btn-primary">Créer le scrutin (brouillon)</button>
            <a href="{{ route('admin.scrutins.index') }}" class="fb-btn fb-btn-ghost">Annuler</a>
        </div>
    </div>
</form>

</x-layouts.admin>
