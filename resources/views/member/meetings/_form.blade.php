<div class="fb-field">
    <label for="title" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">Titre <span style="color:var(--brique-500)">*</span></label>
    <input
        type="text"
        id="title"
        name="title"
        value="{{ old('title') }}"
        maxlength="255"
        required
        class="fb-input {{ $errors->has('title') ? 'is-invalid' : '' }}"
        placeholder="Ex. Réunion mensuelle du cercle"
    >
    @error('title')
        <div class="fb-error">{{ $message }}</div>
    @enderror
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="fb-field">
        <label for="scheduled_at" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">Date et heure <span style="color:var(--brique-500)">*</span></label>
        <input
            type="datetime-local"
            id="scheduled_at"
            name="scheduled_at"
            value="{{ old('scheduled_at') }}"
            required
            class="fb-input {{ $errors->has('scheduled_at') ? 'is-invalid' : '' }}"
        >
        @error('scheduled_at')
            <div class="fb-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="fb-field">
        <label for="duration_minutes" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">Durée estimée <span style="color:var(--fg-tertiary);font-weight:400;">(minutes, optionnel)</span></label>
        <input
            type="number"
            id="duration_minutes"
            name="duration_minutes"
            value="{{ old('duration_minutes') }}"
            min="1"
            class="fb-input {{ $errors->has('duration_minutes') ? 'is-invalid' : '' }}"
            placeholder="Ex. 90"
        >
        @error('duration_minutes')
            <div class="fb-error">{{ $message }}</div>
        @enderror
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="fb-field">
        <label for="location" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">Lieu <span style="color:var(--fg-tertiary);font-weight:400;">(optionnel)</span></label>
        <input
            type="text"
            id="location"
            name="location"
            value="{{ old('location') }}"
            maxlength="255"
            class="fb-input {{ $errors->has('location') ? 'is-invalid' : '' }}"
            placeholder="Ex. Salle des fêtes, 5 rue de la Paix"
        >
        @error('location')
            <div class="fb-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="fb-field">
        <label for="visio_url" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">Lien visio <span style="color:var(--fg-tertiary);font-weight:400;">(optionnel)</span></label>
        <input
            type="url"
            id="visio_url"
            name="visio_url"
            value="{{ old('visio_url') }}"
            class="fb-input {{ $errors->has('visio_url') ? 'is-invalid' : '' }}"
            placeholder="https://meet.example.com/..."
        >
        @error('visio_url')
            <div class="fb-error">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Ordre du jour --}}
<div style="border-top:1px solid var(--border-subtle);padding-top:20px;margin-top:4px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <h2 style="font-size:14px;font-weight:600;color:var(--fg-secondary);margin:0;">Ordre du jour <span style="color:var(--brique-500)">*</span></h2>
    </div>

    @error('agenda_items')
        <div class="fb-error" style="margin-bottom:12px;">{{ $message }}</div>
    @enderror

    <div id="agenda-items-list" style="display:flex;flex-direction:column;gap:10px;">
        @php $oldItems = old('agenda_items', [['title' => '', 'duration_minutes' => '']]); @endphp
        @foreach ($oldItems as $i => $item)
            <div class="agenda-item" style="display:grid;grid-template-columns:1fr auto auto;gap:8px;align-items:start;">
                <div class="fb-field" style="margin:0;">
                    <input
                        type="text"
                        name="agenda_items[{{ $i }}][title]"
                        value="{{ $item['title'] ?? '' }}"
                        maxlength="255"
                        required
                        class="fb-input {{ $errors->has('agenda_items.'.$i.'.title') ? 'is-invalid' : '' }}"
                        placeholder="Point {{ $i + 1 }} de l'ordre du jour"
                    >
                    @error('agenda_items.'.$i.'.title')
                        <div class="fb-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="fb-field" style="margin:0;width:110px;">
                    <input
                        type="number"
                        name="agenda_items[{{ $i }}][duration_minutes]"
                        value="{{ $item['duration_minutes'] ?? '' }}"
                        min="1"
                        class="fb-input {{ $errors->has('agenda_items.'.$i.'.duration_minutes') ? 'is-invalid' : '' }}"
                        placeholder="min."
                    >
                </div>
                <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm remove-agenda-item" style="margin-top:1px;color:var(--fg-tertiary);" aria-label="Supprimer ce point">✕</button>
            </div>
        @endforeach
    </div>

    <button type="button" id="add-agenda-item" class="fb-btn fb-btn-ghost fb-btn-sm" style="margin-top:10px;">+ Ajouter un point</button>
</div>

<script>
(function () {
    const list = document.getElementById('agenda-items-list');
    const addBtn = document.getElementById('add-agenda-item');

    function getIndex() {
        return list.querySelectorAll('.agenda-item').length;
    }

    function addItem() {
        const index = getIndex();
        const div = document.createElement('div');
        div.className = 'agenda-item';
        div.style.cssText = 'display:grid;grid-template-columns:1fr auto auto;gap:8px;align-items:start;';
        div.innerHTML = `
            <div class="fb-field" style="margin:0;">
                <input type="text" name="agenda_items[${index}][title]" maxlength="255" required
                    class="fb-input" placeholder="Point ${index + 1} de l'ordre du jour">
            </div>
            <div class="fb-field" style="margin:0;width:110px;">
                <input type="number" name="agenda_items[${index}][duration_minutes]" min="1"
                    class="fb-input" placeholder="min.">
            </div>
            <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm remove-agenda-item"
                style="margin-top:1px;color:var(--fg-tertiary);" aria-label="Supprimer ce point">✕</button>
        `;
        list.appendChild(div);
        div.querySelector('input[type="text"]').focus();
        bindRemove(div.querySelector('.remove-agenda-item'));
    }

    function bindRemove(btn) {
        btn.addEventListener('click', function () {
            if (list.querySelectorAll('.agenda-item').length > 1) {
                btn.closest('.agenda-item').remove();
                renumberPlaceholders();
            }
        });
    }

    function renumberPlaceholders() {
        list.querySelectorAll('.agenda-item').forEach(function (item, i) {
            const titleInput = item.querySelector('input[type="text"]');
            if (titleInput) titleInput.placeholder = 'Point ' + (i + 1) + ' de l\'ordre du jour';
            item.querySelectorAll('input').forEach(function (input) {
                input.name = input.name.replace(/\[\d+\]/, '[' + i + ']');
            });
        });
    }

    list.querySelectorAll('.remove-agenda-item').forEach(bindRemove);
    addBtn.addEventListener('click', addItem);
})();
</script>
