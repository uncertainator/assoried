{{-- Section : Participants --}}
<div class="fb-field">
    <label for="participants" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">Participants présents</label>
    <textarea
        id="participants"
        name="participants"
        rows="3"
        maxlength="5000"
        class="fb-input {{ $errors->has('participants') ? 'is-invalid' : '' }}"
        placeholder="Noms ou pseudos des personnes présentes..."
        style="resize:vertical;"
    >{{ old('participants', $report->participants ?? '') }}</textarea>
    @error('participants')
        <div class="fb-error">{{ $message }}</div>
    @enderror
</div>

{{-- Section : Suivi de l'ordre du jour --}}
<div style="border-top:1px solid var(--border-subtle);padding-top:20px;margin-top:4px;">
    <h2 style="font-size:14px;font-weight:600;color:var(--fg-secondary);margin:0 0 14px;">Suivi de l'ordre du jour</h2>

    @if ($meeting->agendaItems->isEmpty())
        <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucun point d'ordre du jour pour cette réunion.</p>
    @else
        <div style="display:flex;flex-direction:column;gap:14px;">
            @foreach ($meeting->agendaItems as $item)
                @php $noteValue = old('agenda_notes.'.$item->id, $report->agenda_notes[$item->id] ?? ''); @endphp
                <div class="fb-field" style="margin:0;">
                    <label style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:flex;gap:8px;align-items:baseline;">
                        <span style="font-size:12px;font-weight:700;color:var(--fg-tertiary);min-width:18px;">{{ $item->position }}.</span>
                        <span>{{ $item->title }}</span>
                        @if ($item->duration_minutes)
                            <span style="font-size:12px;color:var(--fg-tertiary);font-weight:400;">{{ $item->duration_minutes }} min</span>
                        @endif
                    </label>
                    <textarea
                        name="agenda_notes[{{ $item->id }}]"
                        rows="3"
                        maxlength="2000"
                        class="fb-input {{ $errors->has('agenda_notes.'.$item->id) ? 'is-invalid' : '' }}"
                        placeholder="Résumé des échanges sur ce point..."
                        style="resize:vertical;"
                    >{{ $noteValue }}</textarea>
                    @error('agenda_notes.'.$item->id)
                        <div class="fb-error">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Section : Décisions prises --}}
<div style="border-top:1px solid var(--border-subtle);padding-top:20px;margin-top:4px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <h2 style="font-size:14px;font-weight:600;color:var(--fg-secondary);margin:0;">Décisions prises</h2>
    </div>
    <div id="decisions-list" style="display:flex;flex-direction:column;gap:8px;">
        @php $decisions = old('decisions', $report->decisions ?? []); @endphp
        @forelse ($decisions as $i => $decision)
            <div class="decision-item" style="display:grid;grid-template-columns:1fr auto;gap:8px;align-items:start;">
                <input
                    type="text"
                    name="decisions[{{ $i }}]"
                    value="{{ $decision }}"
                    maxlength="500"
                    class="fb-input"
                    placeholder="Décision prise..."
                >
                <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm remove-decision" style="color:var(--fg-tertiary);" aria-label="Supprimer">✕</button>
            </div>
        @empty
            <div class="decision-item" style="display:grid;grid-template-columns:1fr auto;gap:8px;align-items:start;">
                <input type="text" name="decisions[0]" value="" maxlength="500" class="fb-input" placeholder="Décision prise...">
                <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm remove-decision" style="color:var(--fg-tertiary);" aria-label="Supprimer">✕</button>
            </div>
        @endforelse
    </div>
    <button type="button" id="add-decision" class="fb-btn fb-btn-ghost fb-btn-sm" style="margin-top:10px;">+ Ajouter une décision</button>
</div>

{{-- Section : Points ouverts / reportés --}}
<div style="border-top:1px solid var(--border-subtle);padding-top:20px;margin-top:4px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <h2 style="font-size:14px;font-weight:600;color:var(--fg-secondary);margin:0;">Points ouverts / reportés</h2>
    </div>
    <div id="open-points-list" style="display:flex;flex-direction:column;gap:8px;">
        @php $openPoints = old('open_points', $report->open_points ?? []); @endphp
        @forelse ($openPoints as $i => $point)
            <div class="open-point-item" style="display:grid;grid-template-columns:1fr auto;gap:8px;align-items:start;">
                <input
                    type="text"
                    name="open_points[{{ $i }}]"
                    value="{{ $point }}"
                    maxlength="500"
                    class="fb-input"
                    placeholder="Point à reporter..."
                >
                <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm remove-open-point" style="color:var(--fg-tertiary);" aria-label="Supprimer">✕</button>
            </div>
        @empty
            <div class="open-point-item" style="display:grid;grid-template-columns:1fr auto;gap:8px;align-items:start;">
                <input type="text" name="open_points[0]" value="" maxlength="500" class="fb-input" placeholder="Point à reporter...">
                <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm remove-open-point" style="color:var(--fg-tertiary);" aria-label="Supprimer">✕</button>
            </div>
        @endforelse
    </div>
    <button type="button" id="add-open-point" class="fb-btn fb-btn-ghost fb-btn-sm" style="margin-top:10px;">+ Ajouter un point</button>
</div>

{{-- Section : Notes libres --}}
<div style="border-top:1px solid var(--border-subtle);padding-top:20px;margin-top:4px;">
    <div class="fb-field" style="margin:0;">
        <label for="free_notes" style="font-size:13px;font-weight:500;color:var(--fg-secondary);margin-bottom:4px;display:block;">Notes libres <span style="color:var(--fg-tertiary);font-weight:400;">(optionnel)</span></label>
        <textarea
            id="free_notes"
            name="free_notes"
            rows="4"
            maxlength="5000"
            class="fb-input {{ $errors->has('free_notes') ? 'is-invalid' : '' }}"
            placeholder="Informations complémentaires, observations..."
            style="resize:vertical;"
        >{{ old('free_notes', $report->free_notes ?? '') }}</textarea>
        @error('free_notes')
            <div class="fb-error">{{ $message }}</div>
        @enderror
    </div>
</div>

<script>
(function () {
    function makeListManager(listId, addBtnId, itemClass, removeBtnClass, namePrefix) {
        const list = document.getElementById(listId);
        const addBtn = document.getElementById(addBtnId);

        function getIndex() {
            return list.querySelectorAll('.' + itemClass).length;
        }

        function addItem() {
            const index = getIndex();
            const div = document.createElement('div');
            div.className = itemClass;
            div.style.cssText = 'display:grid;grid-template-columns:1fr auto;gap:8px;align-items:start;';
            div.innerHTML = `
                <input type="text" name="${namePrefix}[${index}]" value="" maxlength="500"
                    class="fb-input" placeholder="${addBtn.dataset.placeholder || ''}">
                <button type="button" class="fb-btn fb-btn-ghost fb-btn-sm ${removeBtnClass}"
                    style="color:var(--fg-tertiary);" aria-label="Supprimer">✕</button>
            `;
            list.appendChild(div);
            div.querySelector('input').focus();
            bindRemove(div.querySelector('.' + removeBtnClass));
        }

        function bindRemove(btn) {
            btn.addEventListener('click', function () {
                btn.closest('.' + itemClass).remove();
                renumber();
            });
        }

        function renumber() {
            list.querySelectorAll('.' + itemClass).forEach(function (item, i) {
                const input = item.querySelector('input');
                if (input) input.name = namePrefix + '[' + i + ']';
            });
        }

        list.querySelectorAll('.' + removeBtnClass).forEach(bindRemove);
        addBtn.addEventListener('click', addItem);
    }

    makeListManager('decisions-list', 'add-decision', 'decision-item', 'remove-decision', 'decisions');
    makeListManager('open-points-list', 'add-open-point', 'open-point-item', 'remove-open-point', 'open_points');
})();
</script>
