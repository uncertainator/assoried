@if ($errors->any())
    <div class="flash-error">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

<div class="fb-field">
    <label for="name">Nom du service</label>
    <input type="text" id="name" name="name" class="fb-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
           value="{{ old('name', $service->name ?? '') }}" required maxlength="150">
    @error('name')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4"
              class="fb-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}" required>{{ old('description', $service->description ?? '') }}</textarea>
    @error('description')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="use_cases">Cas d'usage typiques</label>
    <textarea id="use_cases" name="use_cases" rows="3"
              class="fb-textarea {{ $errors->has('use_cases') ? 'is-invalid' : '' }}" required>{{ old('use_cases', $service->use_cases ?? '') }}</textarea>
    @error('use_cases')<span class="fb-error">{{ $message }}</span>@enderror
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
