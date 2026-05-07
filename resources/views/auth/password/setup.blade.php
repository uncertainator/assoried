<x-layouts.member title="Définir un mot de passe — La Fabrique">

<div style="max-width:480px;margin:0 auto;">
    <div class="ea-topbar" style="margin-bottom:28px;">
        <div>
            <h1 class="ea-greeting">Définir un <em>mot de passe</em></h1>
        </div>
    </div>

    <div class="ea-panel" style="margin-bottom:20px;">
        <div class="ea-panel-head">
            <h2 class="ea-panel-title">Sécurisez votre compte</h2>
        </div>
        <p style="font-size:14px;color:var(--fg-secondary);margin:0 0 20px;line-height:1.6;">
            Vous pouvez définir un mot de passe pour vous connecter sans email à chaque fois.
            Vous pourrez toujours utiliser un lien magique.
        </p>

        @if ($errors->any())
            <div class="flash-error" style="margin-bottom:16px;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('account.password.store') }}">
            @csrf
            <div class="ea-field">
                <label for="password">Nouveau mot de passe</label>
                <input type="password" id="password" name="password"
                       autocomplete="new-password"
                       placeholder="8 caractères minimum">
                @error('password')<span class="ea-error">{{ $message }}</span>@enderror
            </div>
            <div class="ea-field" style="margin-bottom:20px;">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       autocomplete="new-password">
            </div>
            <button type="submit" class="ea-btn-primary">Définir mon mot de passe →</button>
        </form>
    </div>

    {{-- Option "Plus tard" --}}
    <div class="ea-panel" style="background:var(--bg-surface-2);">
        <div class="ea-panel-head">
            <h2 class="ea-panel-title" style="font-size:14px;color:var(--fg-secondary);">Pas maintenant</h2>
        </div>
        <form method="POST" action="{{ route('account.password.dismiss') }}">
            @csrf
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                <input type="checkbox" id="dont_show_again" name="dont_show_again" value="1"
                       style="accent-color:var(--brique-500);">
                <label for="dont_show_again" style="font-size:13px;color:var(--fg-secondary);cursor:pointer;">
                    Ne plus me proposer
                </label>
            </div>
            <button type="submit" class="fb-btn fb-btn-outline fb-btn-sm">
                Plus tard
            </button>
        </form>
    </div>
</div>

</x-layouts.member>
