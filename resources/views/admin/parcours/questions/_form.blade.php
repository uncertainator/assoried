@if ($errors->any())
    <div class="flash-error">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

<div class="fb-field">
    <label for="label">Libellé de la question</label>
    <input type="text" id="label" name="label" class="fb-input {{ $errors->has('label') ? 'is-invalid' : '' }}"
           value="{{ old('label', $question->label ?? '') }}" required maxlength="255"
           placeholder="Ex: Quel est votre contexte ?">
    @error('label')<span class="fb-error">{{ $message }}</span>@enderror
</div>

<div class="fb-field">
    <label for="sort_order">Ordre d'affichage</label>
    <input type="number" id="sort_order" name="sort_order" class="fb-input"
           value="{{ old('sort_order', $question->sort_order ?? 0) }}" min="0">
</div>

{{-- Options --}}
<div style="margin-top:28px;">
    <div style="font-weight:600;color:var(--fg-primary);margin-bottom:12px;font-size:15px;">
        Options de réponse
    </div>

    <div id="options-container" style="display:flex;flex-direction:column;gap:12px;">
        @foreach (old('options', isset($question) ? $question->options->toArray() : []) as $i => $option)
            <div class="option-row" style="background:var(--bg-surface-3,var(--bg-surface-2));border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:16px;">
                @if (!empty($option['id']))
                    <input type="hidden" name="options[{{ $i }}][id]" value="{{ $option['id'] }}">
                @endif
                <div class="fb-field" style="margin-bottom:10px;">
                    <label>Libellé de l'option</label>
                    <input type="text" name="options[{{ $i }}][label]" class="fb-input"
                           value="{{ $option['label'] ?? '' }}" required maxlength="255">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:10px;">
                    <div class="fb-field">
                        <label>Question suivante</label>
                        <select name="options[{{ $i }}][next_question_id]" class="fb-input">
                            <option value="">— aucune —</option>
                            @foreach ($questions as $q)
                                <option value="{{ $q->id }}"
                                    {{ (string)($option['next_question_id'] ?? '') === (string)$q->id ? 'selected' : '' }}>
                                    {{ Str::limit($q->label, 50) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fb-field">
                        <label>Ou : service destination</label>
                        <select name="options[{{ $i }}][service_id]" class="fb-input">
                            <option value="">— aucun —</option>
                            @foreach ($services as $s)
                                <option value="{{ $s->id }}"
                                    {{ (string)($option['service_id'] ?? '') === (string)$s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="fb-field" style="margin-bottom:0;">
                    <label>Ordre</label>
                    <input type="number" name="options[{{ $i }}][sort_order]" class="fb-input"
                           value="{{ $option['sort_order'] ?? $i }}" min="0" style="max-width:80px;">
                </div>
            </div>
        @endforeach
    </div>

    <button type="button" id="add-option" class="fb-btn fb-btn-ghost" style="margin-top:12px;font-size:13px;">
        + Ajouter une option
    </button>
</div>

<script>
    (function () {
        const container = document.getElementById('options-container');
        let idx = container.querySelectorAll('.option-row').length;

        document.getElementById('add-option').addEventListener('click', function () {
            const tpl = `<div class="option-row" style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:16px;">
                <div class="fb-field" style="margin-bottom:10px;">
                    <label>Libellé de l'option</label>
                    <input type="text" name="options[${idx}][label]" class="fb-input" required maxlength="255">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:10px;">
                    <div class="fb-field">
                        <label>Question suivante</label>
                        <select name="options[${idx}][next_question_id]" class="fb-input">
                            <option value="">— aucune —</option>
                            @foreach ($questions as $q)
                            <option value="{{ $q->id }}">{{ Str::limit($q->label, 50) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fb-field">
                        <label>Ou : service destination</label>
                        <select name="options[${idx}][service_id]" class="fb-input">
                            <option value="">— aucun —</option>
                            @foreach ($services as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="fb-field" style="margin-bottom:0;">
                    <label>Ordre</label>
                    <input type="number" name="options[${idx}][sort_order]" class="fb-input" value="${idx}" min="0" style="max-width:80px;">
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', tpl);
            idx++;
        });
    })();
</script>
