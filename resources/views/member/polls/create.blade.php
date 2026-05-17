<x-layouts.member title="Nouveau sondage — La Fabrique">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Nouveau sondage</h1>
        @if (isset($circle))
            <div class="ea-greeting-sub">Cercle : {{ $circle->name }}</div>
        @else
            <div class="ea-greeting-sub">Sondage au niveau association</div>
        @endif
    </div>
    <a href="{{ route('member.polls.index') }}" class="fb-btn fb-btn-ghost fb-btn-sm">← Retour</a>
</div>

@if ($errors->any())
    <div style="background:var(--brique-100);border:1px solid var(--brique-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--brique-700);">
        <ul style="margin:0;padding-left:16px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:24px 28px;max-width:620px;"
     x-data="{ type: '{{ old('type', 'yes_no') }}' }">

    <form method="POST"
          action="{{ isset($circle)
              ? route('member.circles.polls.store', $circle)
              : route('member.polls.store') }}">
        @csrf

        {{-- Titre --}}
        <div style="margin-bottom:18px;">
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">Titre du sondage</label>
            <input
                type="text"
                name="title"
                value="{{ old('title') }}"
                maxlength="200"
                required
                style="width:100%;padding:9px 12px;border:1px solid var(--border-subtle);border-radius:6px;font-size:14px;background:var(--surface-default);color:var(--fg-primary);"
            >
        </div>

        {{-- Type --}}
        <div style="margin-bottom:18px;">
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">Type de sondage</label>
            <select
                name="type"
                x-model="type"
                style="width:100%;padding:9px 12px;border:1px solid var(--border-subtle);border-radius:6px;font-size:14px;background:var(--surface-default);color:var(--fg-primary);"
            >
                <option value="yes_no" @selected(old('type', 'yes_no') === 'yes_no')>Oui / Non</option>
                <option value="multiple" @selected(old('type') === 'multiple')>Choix multiple</option>
            </select>
        </div>

        {{-- Options (choix multiple uniquement) --}}
        <div x-show="type === 'multiple'" style="margin-bottom:18px;"
             x-data="{ options: {{ json_encode(old('options', ['', ''])) }} }">
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">Options</label>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <template x-for="(opt, i) in options" :key="i">
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input
                            type="text"
                            :name="'options[' + i + ']'"
                            x-model="options[i]"
                            maxlength="200"
                            placeholder="Option…"
                            style="flex:1;padding:9px 12px;border:1px solid var(--border-subtle);border-radius:6px;font-size:14px;background:var(--surface-default);color:var(--fg-primary);"
                        >
                        <button type="button" @click="options.splice(i, 1)"
                                x-show="options.length > 2"
                                style="color:var(--brique-500);font-size:18px;background:none;border:none;cursor:pointer;line-height:1;">&times;</button>
                    </div>
                </template>
            </div>
            <button type="button" @click="options.push('')"
                    style="margin-top:10px;font-size:13px;color:var(--brique-500);background:none;border:none;cursor:pointer;padding:0;">
                + Ajouter une option
            </button>
        </div>

        {{-- Date de clôture --}}
        <div style="margin-bottom:24px;">
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">Date de clôture</label>
            <input
                type="datetime-local"
                name="closes_at"
                value="{{ old('closes_at') }}"
                required
                style="width:100%;padding:9px 12px;border:1px solid var(--border-subtle);border-radius:6px;font-size:14px;background:var(--surface-default);color:var(--fg-primary);"
            >
        </div>

        <button type="submit" class="fb-btn fb-btn-primary">Créer le sondage</button>
    </form>
</div>

</x-layouts.member>
