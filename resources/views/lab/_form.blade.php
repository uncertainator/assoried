@if ($errors->any())
    <div class="flash-error" style="margin-bottom:16px;">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="fb-field">
    <label for="title">Titre du service</label>
    <input type="text" id="title" name="title"
           class="fb-input {{ $errors->has('title') ? 'is-invalid' : '' }}"
           value="{{ old('title', $service->title ?? '') }}"
           placeholder="ex : Animation d'atelier de créativité"
           required>
    @error('title')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="category">Catégorie</label>
    <input type="text" id="category" name="category"
           class="fb-input {{ $errors->has('category') ? 'is-invalid' : '' }}"
           value="{{ old('category', $service->category ?? '') }}"
           list="category-suggestions"
           placeholder="ex : Facilitation"
           required>
    <datalist id="category-suggestions">
        <option value="Facilitation">
        <option value="Gestion de projet">
        <option value="Innovation">
        <option value="Communication">
        <option value="Autre">
    </datalist>
    @error('category')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="description">Description</label>
    <textarea id="description" name="description"
              class="fb-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}"
              rows="6"
              placeholder="Décrivez l'offre d'accompagnement proposée par le Lab…"
              required>{{ old('description', $service->description ?? '') }}</textarea>
    @error('description')<span class="fb-error">{{ $message }}</span>@enderror
</div>
