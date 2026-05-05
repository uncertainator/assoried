@if ($errors->any())
    <div class="flash-error">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

@isset($circle)
    {{-- Editing: slug is read-only --}}
@else
    <div class="fb-field">
        <label for="slug">Slug (identifiant URL — lettres minuscules, chiffres, tirets)</label>
        <input type="text" id="slug" name="slug" class="fb-input {{ $errors->has('slug') ? 'is-invalid' : '' }}"
               placeholder="ex: bien-vivre" value="{{ old('slug') }}" pattern="[a-z0-9\-]+" required>
        @error('slug')<span class="fb-error">{{ $message }}</span>@enderror
        <span class="fb-help">Non modifiable après création.</span>
    </div>
@endisset

<div class="fb-field">
    <label for="name">Nom du cercle</label>
    <input type="text" id="name" name="name" class="fb-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
           value="{{ old('name', $circle->name ?? '') }}" required>
    @error('name')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="description">Description</label>
    <textarea id="description" name="description" rows="3"
              class="fb-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}">{{ old('description', $circle->description ?? '') }}</textarea>
    @error('description')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="max_members">Nombre maximum de membres (laisser vide = illimité)</label>
    <input type="number" id="max_members" name="max_members" class="fb-input"
           value="{{ old('max_members', $circle->max_members ?? '') }}" min="1">
    @error('max_members')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-checkbox" style="margin-top:8px;">
    <input type="checkbox" id="is_active" name="is_active" value="1"
           {{ old('is_active', $circle->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active">Cercle actif (visible et accessible aux adhérents)</label>
</div>
