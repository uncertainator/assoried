@if ($errors->any())
    <div class="flash-error">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

<div class="fb-field">
    <label for="slug">Slug (identifiant URL)</label>
    <input type="text" id="slug" name="slug" class="fb-input {{ $errors->has('slug') ? 'is-invalid' : '' }}"
           value="{{ old('slug', $service->slug ?? '') }}" maxlength="100"
           placeholder="ex : co-developpement">
    @error('slug')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="name">Nom du service</label>
    <input type="text" id="name" name="name" class="fb-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
           value="{{ old('name', $service->name ?? '') }}" required maxlength="150">
    @error('name')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="branche">Branche (Explorer / Construire / Accélérer)</label>
    <input type="text" id="branche" name="branche" class="fb-input {{ $errors->has('branche') ? 'is-invalid' : '' }}"
           value="{{ old('branche', $service->branche ?? '') }}" maxlength="80"
           placeholder="ex : Explorer / Construire">
    @error('branche')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="description">Accroche (1 phrase bénéfice)</label>
    <textarea id="description" name="description" rows="2"
              class="fb-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}" required>{{ old('description', $service->description ?? '') }}</textarea>
    @error('description')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="pour_qui">Pour qui (profils cibles)</label>
    <textarea id="pour_qui" name="pour_qui" rows="2"
              class="fb-textarea {{ $errors->has('pour_qui') ? 'is-invalid' : '' }}">{{ old('pour_qui', $service->pour_qui ?? '') }}</textarea>
    @error('pour_qui')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="use_cases">Cas d'usage (1 par ligne, 3 recommandés)</label>
    <textarea id="use_cases" name="use_cases" rows="4"
              class="fb-textarea {{ $errors->has('use_cases') ? 'is-invalid' : '' }}"
              placeholder="Situation 1&#10;Situation 2&#10;Situation 3">{{ old('use_cases', implode("\n", $service->use_cases ?? [])) }}</textarea>
    @error('use_cases')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="ce_que_ca_produit">Ce que ça produit (livrable / résultat)</label>
    <textarea id="ce_que_ca_produit" name="ce_que_ca_produit" rows="2"
              class="fb-textarea {{ $errors->has('ce_que_ca_produit') ? 'is-invalid' : '' }}">{{ old('ce_que_ca_produit', $service->ce_que_ca_produit ?? '') }}</textarea>
    @error('ce_que_ca_produit')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="format">Format (durée, modalité)</label>
    <input type="text" id="format" name="format" class="fb-input {{ $errors->has('format') ? 'is-invalid' : '' }}"
           value="{{ old('format', $service->format ?? '') }}" maxlength="200"
           placeholder="ex : Atelier 1 à 2 jours">
    @error('format')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="cta_type">Type d'action (CTA)</label>
    <select id="cta_type" name="cta_type" class="fb-input {{ $errors->has('cta_type') ? 'is-invalid' : '' }}" required>
        @foreach (\App\Enums\ParcoursCtaType::cases() as $type)
            <option value="{{ $type->value }}"
                {{ old('cta_type', $service->cta_type->value ?? '') === $type->value ? 'selected' : '' }}>
                {{ $type->label() }}
            </option>
        @endforeach
    </select>
    @error('cta_type')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="cta_value">URL ou adresse email du CTA</label>
    <input type="text" id="cta_value" name="cta_value" class="fb-input {{ $errors->has('cta_value') ? 'is-invalid' : '' }}"
           value="{{ old('cta_value', $service->cta_value ?? '') }}" required maxlength="512"
           placeholder="https://... ou contact@...">
    @error('cta_value')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="sort_order">Ordre d'affichage</label>
    <input type="number" id="sort_order" name="sort_order" class="fb-input"
           value="{{ old('sort_order', $service->sort_order ?? 0) }}" min="0">
</div>

<div class="fb-checkbox" style="margin-top:8px;">
    <input type="checkbox" id="is_active" name="is_active" value="1"
           {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active">Service actif</label>
</div>
