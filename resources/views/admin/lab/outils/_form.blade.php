<div style="display:flex;flex-direction:column;gap:20px;">
    {{-- Titre --}}
    <div>
        <label for="title" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
            Titre <span style="color:var(--brique-500);">*</span>
        </label>
        <input type="text" id="title" name="title" value="{{ old('title', $tool->title ?? '') }}"
               maxlength="150" required
               style="width:100%;padding:10px 12px;border:1px solid var(--border-subtle);border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
        @error('title')
            <div style="margin-top:4px;font-size:var(--text-xs);color:var(--brique-600);">{{ $message }}</div>
        @enderror
    </div>

    {{-- Description --}}
    <div>
        <label for="description" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">Description</label>
        <textarea id="description" name="description" rows="4"
                  style="width:100%;padding:10px 12px;border:1px solid var(--border-subtle);border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);resize:vertical;box-sizing:border-box;">{{ old('description', $tool->description ?? '') }}</textarea>
        @error('description')
            <div style="margin-top:4px;font-size:var(--text-xs);color:var(--brique-600);">{{ $message }}</div>
        @enderror
    </div>

    {{-- Catégorie --}}
    <div>
        <label for="category" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">Catégorie</label>
        <input type="text" id="category" name="category" value="{{ old('category', $tool->category ?? '') }}"
               maxlength="100"
               placeholder="ex. Design Thinking, Facilitation…"
               style="width:100%;padding:10px 12px;border:1px solid var(--border-subtle);border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);box-sizing:border-box;">
        @error('category')
            <div style="margin-top:4px;font-size:var(--text-xs);color:var(--brique-600);">{{ $message }}</div>
        @enderror
    </div>

    {{-- Fichier PDF --}}
    <div>
        <label for="file" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
            Fichier PDF {{ isset($tool) ? '' : '<span style="color:var(--brique-500);">*</span>' }}
        </label>
        @isset($tool)
            <div style="margin-bottom:8px;font-size:var(--text-sm);color:var(--fg-secondary);">
                Fichier actuel : <span style="font-family:monospace;">{{ $tool->file_path }}</span>
            </div>
        @endisset
        <input type="file" id="file" name="file" accept="application/pdf"
               {{ isset($tool) ? '' : 'required' }}
               style="font-size:var(--text-sm);color:var(--fg-primary);">
        <div style="margin-top:4px;font-size:var(--text-xs);color:var(--fg-tertiary);">Format PDF uniquement, 20 Mo maximum.</div>
        @error('file')
            <div style="margin-top:4px;font-size:var(--text-xs);color:var(--brique-600);">{{ $message }}</div>
        @enderror
    </div>

    {{-- Actif --}}
    <div style="display:flex;align-items:center;gap:10px;">
        <input type="hidden" name="active" value="0">
        <input type="checkbox" id="active" name="active" value="1"
               {{ old('active', $tool->active ?? true) ? 'checked' : '' }}
               style="width:16px;height:16px;cursor:pointer;">
        <label for="active" style="font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);cursor:pointer;">
            Outil actif (visible dans la bibliothèque)
        </label>
    </div>
</div>
