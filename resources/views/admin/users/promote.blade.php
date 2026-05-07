<x-layouts.admin title="Promouvoir un référent — Admin La Fabrique">

<div style="margin-bottom:28px;">
    <div class="fb-eyebrow">Administration</div>
    <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
        Promouvoir en référent
    </h1>
</div>

<div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:28px;max-width:520px;box-shadow:var(--shadow-sm);">

    <p style="margin:0 0 20px;color:var(--fg-secondary);">
        Adhérent : <strong>{{ $user->name ?: $user->email }}</strong>
    </p>

    @if ($circles->isEmpty())
        <p style="color:var(--fg-tertiary);font-style:italic;">
            Tous les cercles ont déjà un référent. Rétrogradez un référent existant pour libérer une place.
        </p>
    @else
        <form method="POST" action="{{ route('admin.users.promote', $user) }}">
            @csrf

            <div class="fb-form-group">
                <label for="circle_id" class="fb-label">Cercle à assigner</label>
                <select id="circle_id" name="circle_id"
                        class="fb-select @error('circle_id') is-invalid @enderror">
                    <option value="">— Sélectionner un cercle —</option>
                    @foreach ($circles as $circle)
                        <option value="{{ $circle->id }}" {{ old('circle_id') == $circle->id ? 'selected' : '' }}>
                            {{ $circle->name }}
                        </option>
                    @endforeach
                </select>
                @error('circle_id')
                    <div class="fb-field-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex;gap:12px;margin-top:24px;">
                <button type="submit" class="fb-btn fb-btn-primary">
                    Confirmer la promotion
                </button>
                <a href="{{ route('admin.users.index') }}" class="fb-btn fb-btn-ghost">
                    Annuler
                </a>
            </div>
        </form>
    @endif

</div>

</x-layouts.admin>
