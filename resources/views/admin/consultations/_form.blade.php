@php
    $isEdit = isset($consultation) && $consultation !== null;
    $currentMode = old('mode_recueil', $consultation?->mode_recueil?->value ?? 'avis_libre');
    $currentOptions = old('options', $consultation?->options ?? []);
@endphp

<form method="POST" action="{{ $action }}" x-data="{
    mode: '{{ $currentMode }}',
    options: {{ json_encode(count($currentOptions) >= 2 ? $currentOptions : ['', '']) }}
}">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    @if ($errors->any())
        <div style="background:var(--brique-100);border:1px solid var(--brique-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--brique-700);">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Titre --}}
    <div style="margin-bottom:20px;">
        <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Titre <span style="color:var(--brique-500);">*</span></label>
        <input type="text" name="titre" value="{{ old('titre', $consultation?->titre) }}"
            style="width:100%;border:1px solid var(--border-default);border-radius:8px;padding:10px 12px;font-size:15px;">
        @error('titre') <p style="color:var(--brique-600);font-size:13px;margin-top:4px;">{{ $message }}</p> @enderror
    </div>

    {{-- Description --}}
    <div style="margin-bottom:20px;">
        <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Description</label>
        <textarea name="description" rows="4"
            style="width:100%;border:1px solid var(--border-default);border-radius:8px;padding:10px 12px;font-size:15px;resize:vertical;font-family:inherit;">{{ old('description', $consultation?->description) }}</textarea>
        @error('description') <p style="color:var(--brique-600);font-size:13px;margin-top:4px;">{{ $message }}</p> @enderror
    </div>

    {{-- Date de clôture --}}
    <div style="margin-bottom:20px;">
        <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;">Date de clôture</label>
        <input type="datetime-local" name="date_cloture"
            value="{{ old('date_cloture', $consultation?->date_cloture?->format('Y-m-d\TH:i')) }}"
            style="border:1px solid var(--border-default);border-radius:8px;padding:10px 12px;font-size:15px;">
        <p style="font-size:12px;color:var(--fg-tertiary);margin-top:4px;">Laissez vide pour une consultation sans date de clôture.</p>
        @error('date_cloture') <p style="color:var(--brique-600);font-size:13px;margin-top:4px;">{{ $message }}</p> @enderror
    </div>

    {{-- Mode de recueil --}}
    <div style="margin-bottom:20px;">
        <label style="display:block;font-size:14px;font-weight:500;margin-bottom:8px;">Mode de recueil <span style="color:var(--brique-500);">*</span></label>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach (\App\Enums\ConsultationMode::cases() as $case)
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <input type="radio" name="mode_recueil" value="{{ $case->value }}"
                        x-model="mode"
                        {{ old('mode_recueil', $consultation?->mode_recueil?->value) === $case->value ? 'checked' : '' }}
                        style="width:16px;height:16px;accent-color:var(--brique-500);">
                    <span style="font-size:14px;">{{ $case->label() }}</span>
                </label>
            @endforeach
        </div>
        @error('mode_recueil') <p style="color:var(--brique-600);font-size:13px;margin-top:4px;">{{ $message }}</p> @enderror
    </div>

    {{-- Options (uniquement pour vote_indicatif) --}}
    <div x-show="mode === 'vote_indicatif'" style="margin-bottom:20px;background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:8px;padding:16px;">
        <label style="display:block;font-size:14px;font-weight:500;margin-bottom:10px;">Options de vote <span style="color:var(--brique-500);">*</span></label>
        <div style="display:flex;flex-direction:column;gap:8px;" id="options-container">
            <template x-for="(opt, i) in options" :key="i">
                <div style="display:flex;gap:8px;align-items:center;">
                    <input type="text" :name="`options[${i}]`" x-model="options[i]"
                        style="flex:1;border:1px solid var(--border-default);border-radius:8px;padding:8px 12px;font-size:14px;"
                        placeholder="Option...">
                    <button type="button" @click="options.splice(i, 1)"
                        x-show="options.length > 2"
                        style="color:var(--brique-500);background:none;border:none;cursor:pointer;font-size:18px;line-height:1;">×</button>
                </div>
            </template>
        </div>
        <button type="button" @click="options.push('')"
            style="margin-top:10px;font-size:13px;color:var(--fg-secondary);background:none;border:1px dashed var(--border-default);border-radius:6px;padding:6px 12px;cursor:pointer;width:100%;">
            + Ajouter une option
        </button>
        @error('options') <p style="color:var(--brique-600);font-size:13px;margin-top:4px;">{{ $message }}</p> @enderror
        @error('options.*') <p style="color:var(--brique-600);font-size:13px;margin-top:4px;">{{ $message }}</p> @enderror
    </div>

    {{-- Masquer --}}
    <div style="margin-bottom:28px;">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
            <input type="hidden" name="masque" value="0">
            <input type="checkbox" name="masque" value="1"
                {{ old('masque', $consultation?->masque) ? 'checked' : '' }}
                style="width:16px;height:16px;accent-color:var(--brique-500);">
            <span style="font-size:14px;">Masquer cette consultation au public</span>
        </label>
    </div>

    <div style="display:flex;gap:12px;">
        <button type="submit" class="fb-btn fb-btn-primary">
            {{ $isEdit ? 'Enregistrer les modifications' : 'Créer la consultation' }}
        </button>
        <a href="{{ route('admin.consultations.index') }}" class="fb-btn fb-btn-ghost">Annuler</a>
    </div>
</form>
