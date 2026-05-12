<div class="fb-field">
    <label for="title" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">Titre <span style="color:var(--brique-500)">*</span></label>
    <input
        type="text"
        id="title"
        name="title"
        value="{{ old('title', $event->title ?? '') }}"
        maxlength="150"
        required
        class="fb-input {{ $errors->has('title') ? 'is-invalid' : '' }}"
        placeholder="Ex. Réunion plénière"
    >
    @error('title')
        <div class="fb-error">{{ $message }}</div>
    @enderror
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="fb-field">
        <label for="starts_at" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">Date et heure de début <span style="color:var(--brique-500)">*</span></label>
        <input
            type="datetime-local"
            id="starts_at"
            name="starts_at"
            value="{{ old('starts_at', isset($event) ? $event->starts_at->format('Y-m-d\TH:i') : '') }}"
            required
            class="fb-input {{ $errors->has('starts_at') ? 'is-invalid' : '' }}"
        >
        @error('starts_at')
            <div class="fb-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="fb-field">
        <label for="ends_at" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">Date et heure de fin <span style="color:var(--fg-tertiary);font-weight:400;">(optionnel)</span></label>
        <input
            type="datetime-local"
            id="ends_at"
            name="ends_at"
            value="{{ old('ends_at', isset($event) && $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : '') }}"
            class="fb-input {{ $errors->has('ends_at') ? 'is-invalid' : '' }}"
        >
        @error('ends_at')
            <div class="fb-error">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="fb-field">
    <label for="location" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">Lieu <span style="color:var(--fg-tertiary);font-weight:400;">(optionnel)</span></label>
    <input
        type="text"
        id="location"
        name="location"
        value="{{ old('location', $event->location ?? '') }}"
        maxlength="200"
        class="fb-input {{ $errors->has('location') ? 'is-invalid' : '' }}"
        placeholder="Ex. Salle polyvalente, 10 rue des Acacias"
    >
    @error('location')
        <div class="fb-error">{{ $message }}</div>
    @enderror
</div>

<div class="fb-field">
    <label for="description" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">Description courte <span style="color:var(--fg-tertiary);font-weight:400;">(optionnel)</span></label>
    <textarea
        id="description"
        name="description"
        rows="3"
        class="fb-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}"
        placeholder="Quelques mots sur cet événement…"
    >{{ old('description', $event->description ?? '') }}</textarea>
    @error('description')
        <div class="fb-error">{{ $message }}</div>
    @enderror
</div>
