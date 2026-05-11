<x-layouts.member title="Mon cercle — La Fabrique">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Mon cercle</h1>
        <div class="ea-greeting-sub">Modifiez les informations du cercle <strong>{{ $circle->name }}</strong></div>
    </div>
</div>

<div class="ea-panel" style="max-width:560px;">
    <div style="font-size:12px;color:var(--fg-tertiary);font-family:var(--font-mono);margin-bottom:20px;">
        Slug : {{ $circle->slug }} (non modifiable)
    </div>

    <form method="POST" action="{{ route('referent.circle.update') }}">
        @csrf @method('PUT')

        <div class="fb-form-group">
            <label for="name" class="fb-label">Nom du cercle</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $circle->name) }}"
                class="fb-input @error('name') is-invalid @enderror"
                maxlength="120"
                required
            >
            @error('name')
                <div class="fb-field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="fb-form-group" style="margin-top:16px;">
            <label for="description" class="fb-label">Description</label>
            <textarea
                id="description"
                name="description"
                class="fb-input @error('description') is-invalid @enderror"
                rows="5"
                style="resize:vertical;"
            >{{ old('description', $circle->description) }}</textarea>
            @error('description')
                <div class="fb-field-error">{{ $message }}</div>
            @enderror
        </div>

        <div style="display:flex;gap:12px;margin-top:24px;">
            <button type="submit" class="fb-btn fb-btn-primary">Enregistrer</button>
            <a href="{{ route('referent.requests.index') }}" class="fb-btn fb-btn-ghost">Annuler</a>
        </div>
    </form>
</div>

</x-layouts.member>
