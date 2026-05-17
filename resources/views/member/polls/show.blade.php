<x-layouts.member :title="$poll->title.' — La Fabrique'">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">{{ $poll->title }}</h1>
        <div class="ea-greeting-sub">
            {{ $poll->circle?->name ?? 'Association' }}
            &nbsp;·&nbsp;
            {{ $poll->type->label() }}
            @if ($poll->isClosed())
                &nbsp;·&nbsp;
                <span style="color:var(--fg-tertiary);">Clôturé le {{ $poll->closes_at->translatedFormat('j M Y') }}</span>
            @else
                &nbsp;·&nbsp;
                Clôture le {{ $poll->closes_at->translatedFormat('j M Y à H\hi') }}
            @endif
        </div>
    </div>
    <a href="{{ route('member.polls.index') }}" class="fb-btn fb-btn-ghost fb-btn-sm">← Sondages</a>
</div>

@if (session('success'))
    <div style="background:var(--mousse-100);border:1px solid var(--mousse-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--mousse-700);">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div style="background:var(--brique-100);border:1px solid var(--brique-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--brique-700);">
        {{ session('error') }}
    </div>
@endif

<div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:24px 28px;max-width:560px;">

    @if ($poll->isClosed())
        {{-- ====================================================== --}}
        {{-- Résultats (sondage clôturé) --}}
        {{-- ====================================================== --}}
        <h2 style="font-size:15px;font-weight:600;margin:0 0 20px;">Résultats</h2>

        @php $total = $results['total']; @endphp

        @if ($total === 0)
            <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucun vote enregistré.</p>
        @else
            <p style="font-size:13px;color:var(--fg-tertiary);margin-bottom:16px;">{{ $total }} {{ Str::plural('vote', $total) }} au total</p>

            <div style="display:flex;flex-direction:column;gap:14px;">
                @foreach ($results['breakdown'] as $choice => $count)
                    @php $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:4px;">
                            <span style="font-weight:500;">{{ $choice }}</span>
                            <span style="color:var(--fg-tertiary);">{{ $count }} ({{ $pct }} %)</span>
                        </div>
                        <div style="background:var(--border-subtle);border-radius:4px;height:8px;overflow:hidden;">
                            <div style="background:var(--brique-400);height:100%;width:{{ $pct }}%;border-radius:4px;transition:width .3s;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    @elseif ($hasVoted)
        {{-- ====================================================== --}}
        {{-- Déjà voté --}}
        {{-- ====================================================== --}}
        <p style="font-size:14px;color:var(--fg-secondary);">
            Vous avez déjà participé à ce sondage. Les résultats seront disponibles après la clôture
            le {{ $poll->closes_at->translatedFormat('j M Y à H\hi') }}.
        </p>

    @elseif ($canVote)
        {{-- ====================================================== --}}
        {{-- Formulaire de vote --}}
        {{-- ====================================================== --}}
        <h2 style="font-size:15px;font-weight:600;margin:0 0 20px;">Votre vote</h2>

        @if ($errors->any())
            <div style="background:var(--brique-100);border:1px solid var(--brique-300);border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:14px;color:var(--brique-700);">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('member.polls.vote', $poll) }}">
            @csrf

            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px;">
                @if ($poll->type->value === 'yes_no')
                    @foreach (['oui' => 'Oui', 'non' => 'Non'] as $value => $label)
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:15px;padding:12px 16px;border:1px solid var(--border-subtle);border-radius:8px;background:var(--surface-default);">
                            <input type="radio" name="choice" value="{{ $value }}" {{ old('choice') === $value ? 'checked' : '' }} required>
                            {{ $label }}
                        </label>
                    @endforeach
                @else
                    @foreach ($poll->options ?? [] as $option)
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:15px;padding:12px 16px;border:1px solid var(--border-subtle);border-radius:8px;background:var(--surface-default);">
                            <input type="radio" name="choice" value="{{ $option }}" {{ old('choice') === $option ? 'checked' : '' }} required>
                            {{ $option }}
                        </label>
                    @endforeach
                @endif
            </div>

            <button type="submit" class="fb-btn fb-btn-primary">Voter</button>
        </form>

    @else
        {{-- ====================================================== --}}
        {{-- Pas accès au vote --}}
        {{-- ====================================================== --}}
        <p style="font-size:14px;color:var(--fg-tertiary);">
            Vous n'êtes pas autorisé à voter sur ce sondage.
        </p>
    @endif
</div>

</x-layouts.member>
