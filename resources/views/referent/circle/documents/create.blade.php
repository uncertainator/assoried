<x-layouts.member :title="'Ajouter un document — '.$circle->name.' — La Fabrique'">

<div class="ea-topbar">
    <h1 class="ea-greeting">Ajouter un document — {{ $circle->name }}</h1>
    <a href="{{ route('member.circles.documents.index', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
        ← Annuler
    </a>
</div>

<div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:28px 32px;max-width:640px;">

    @if ($errors->any())
        <div style="background:var(--brique-100);border:1px solid var(--brique-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--brique-700);">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST"
          action="{{ route('referent.circle.documents.store', $circle) }}"
          enctype="multipart/form-data"
          x-data="{ docType: '{{ old('type', 'pdf') }}' }">
        @csrf

        {{-- Type de document --}}
        <div class="fb-field" style="margin-bottom:20px;">
            <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Type de document *</label>
            <div style="display:flex;gap:16px;">
                <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;">
                    <input type="radio" name="type" value="pdf"
                           x-model="docType"
                           {{ old('type', 'pdf') === 'pdf' ? 'checked' : '' }}>
                    Fichier PDF
                </label>
                <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;">
                    <input type="radio" name="type" value="link"
                           x-model="docType"
                           {{ old('type') === 'link' ? 'checked' : '' }}>
                    Lien externe
                </label>
            </div>
            @error('type')
                <span class="fb-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Fichier PDF (affiché si type = pdf) --}}
        <div x-show="docType === 'pdf'" x-cloak style="margin-bottom:16px;">
            <label for="file" style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">
                Fichier PDF * <span style="font-size:12px;color:var(--fg-tertiary);">(max 10 Mo)</span>
            </label>
            <input type="file"
                   id="file"
                   name="file"
                   accept="application/pdf"
                   class="fb-input {{ $errors->has('file') ? 'is-invalid' : '' }}"
                   style="width:100%;">
            @error('file')
                <span class="fb-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- URL lien externe (affiché si type = link) --}}
        <div x-show="docType === 'link'" x-cloak style="margin-bottom:16px;">
            <label for="url" style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">
                URL *
            </label>
            <input type="url"
                   id="url"
                   name="url"
                   value="{{ old('url') }}"
                   placeholder="https://..."
                   class="fb-input {{ $errors->has('url') ? 'is-invalid' : '' }}"
                   style="width:100%;">
            @error('url')
                <span class="fb-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Titre --}}
        <div class="fb-field" style="margin-bottom:16px;">
            <label for="title" style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Titre *</label>
            <input type="text"
                   id="title"
                   name="title"
                   value="{{ old('title') }}"
                   maxlength="255"
                   required
                   class="fb-input {{ $errors->has('title') ? 'is-invalid' : '' }}"
                   style="width:100%;">
            @error('title')
                <span class="fb-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Date du document --}}
        <div class="fb-field" style="margin-bottom:16px;">
            <label for="document_date" style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">
                Date du document *
            </label>
            <input type="date"
                   id="document_date"
                   name="document_date"
                   value="{{ old('document_date', date('Y-m-d')) }}"
                   required
                   class="fb-input {{ $errors->has('document_date') ? 'is-invalid' : '' }}">
            @error('document_date')
                <span class="fb-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Tags (CSV) --}}
        <div class="fb-field" style="margin-bottom:16px;">
            <label for="tags_input" style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">
                Tags <span style="font-size:12px;color:var(--fg-tertiary);">(séparés par des virgules)</span>
            </label>
            <input type="text"
                   id="tags_input"
                   name="tags_input"
                   value="{{ old('tags_input') }}"
                   placeholder="statuts, réunion, rapport"
                   class="fb-input"
                   style="width:100%;">
            @error('tags')
                <span class="fb-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Description --}}
        <div class="fb-field" style="margin-bottom:24px;">
            <label for="description" style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">
                Description <span style="font-size:12px;color:var(--fg-tertiary);">(optionnel)</span>
            </label>
            <textarea id="description"
                      name="description"
                      rows="4"
                      maxlength="5000"
                      class="fb-input {{ $errors->has('description') ? 'is-invalid' : '' }}"
                      style="width:100%;resize:vertical;">{{ old('description') }}</textarea>
            @error('description')
                <span class="fb-error">{{ $message }}</span>
            @enderror
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('member.circles.documents.index', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
                Annuler
            </a>
            <button type="submit" class="fb-btn fb-btn-primary fb-btn-sm">
                Enregistrer
            </button>
        </div>
    </form>
</div>

</x-layouts.member>
