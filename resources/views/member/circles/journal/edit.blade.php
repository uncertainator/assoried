<x-layouts.member :title="'Modifier l\'entrée — '.$circle->name.' — La Fabrique'">

<div class="ea-topbar">
    <h1 class="ea-greeting">Modifier l'entrée — {{ $circle->name }}</h1>
    <a href="{{ route('member.circles.journal.index', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
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

    <form method="POST" action="{{ route('member.circles.journal.update', [$circle, $entry]) }}">
        @csrf @method('PUT')

        <div class="fb-field" style="margin-bottom:16px;">
            <label for="title" style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Titre *</label>
            <input type="text" id="title" name="title"
                   value="{{ old('title', $entry->title) }}"
                   maxlength="255"
                   required
                   class="fb-input {{ $errors->has('title') ? 'is-invalid' : '' }}"
                   style="width:100%;">
            @error('title')
                <span class="fb-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="fb-field" style="margin-bottom:16px;">
            <label for="entry_date" style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Date de l'entrée *</label>
            <input type="date" id="entry_date" name="entry_date"
                   value="{{ old('entry_date', $entry->entry_date->format('Y-m-d')) }}"
                   required
                   class="fb-input {{ $errors->has('entry_date') ? 'is-invalid' : '' }}">
            @error('entry_date')
                <span class="fb-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="fb-field" style="margin-bottom:24px;">
            <label for="content" style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Contenu *</label>
            <textarea id="content" name="content"
                      rows="10"
                      maxlength="10000"
                      required
                      class="fb-input {{ $errors->has('content') ? 'is-invalid' : '' }}"
                      style="width:100%;resize:vertical;">{{ old('content', $entry->content) }}</textarea>
            @error('content')
                <span class="fb-error">{{ $message }}</span>
            @enderror
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('member.circles.journal.index', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Annuler</a>
            <button type="submit" class="fb-btn fb-btn-primary fb-btn-sm">Mettre à jour</button>
        </div>
    </form>
</div>

</x-layouts.member>
