<x-layouts.member :title="$scrutin->title.' — La Fabrique'">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">{{ $scrutin->title }}</h1>
        <div class="ea-greeting-sub">
            @if ($scrutin->isClosed())
                Clôturé le {{ $scrutin->closes_at->translatedFormat('j M Y') }}
            @elseif ($scrutin->isVotable())
                Clôture le {{ $scrutin->closes_at->translatedFormat('j M Y à H\hi') }}
            @else
                Ouverture le {{ $scrutin->opened_at->translatedFormat('j M Y à H\hi') }}
            @endif
        </div>
    </div>
    <a href="{{ route('member.scrutins.index') }}" class="fb-btn fb-btn-ghost fb-btn-sm">← Scrutins</a>
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

@if ($scrutin->description)
    <div style="font-size:14px;color:var(--fg-secondary);margin-bottom:20px;line-height:1.6;">{{ $scrutin->description }}</div>
@endif

<div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:24px 28px;max-width:560px;">

    @if ($scrutin->isClosed())
        {{-- ====================================================== --}}
        {{-- Résultats --}}
        {{-- ====================================================== --}}
        <div style="margin-bottom:16px;">
            @if ($scrutin->result_status)
                <span class="{{ $scrutin->result_status->badgeClass() }}" style="font-size:13px;font-weight:600;padding:4px 12px;border-radius:20px;">
                    {{ $scrutin->result_status->label() }}
                </span>
            @endif
        </div>

        <div style="font-size:13px;color:var(--fg-tertiary);margin-bottom:20px;">
            {{ $scrutin->total_votes }} vote(s) exprimé(s)
            @if ($scrutin->active_members_at_close)
                sur {{ $scrutin->active_members_at_close }} membres actifs
                ({{ $scrutin->active_members_at_close > 0 ? round($scrutin->total_votes / $scrutin->active_members_at_close * 100, 1) : 0 }} % de participation)
            @endif
        </div>

        @if ($scrutin->total_votes > 0)
            <div style="display:flex;flex-direction:column;gap:14px;">
                @foreach ($scrutin->options as $option)
                    @php
                        $count = $voteCounts[$option->id] ?? 0;
                        $pct = $scrutin->total_votes > 0 ? round($count / $scrutin->total_votes * 100) : 0;
                        $isWinner = $scrutin->winningOption?->id === $option->id;
                    @endphp
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:4px;">
                            <span style="font-weight:{{ $isWinner ? '700' : '400' }};color:{{ $isWinner ? 'var(--mousse-700)' : 'var(--fg-primary)' }};">
                                {{ $option->label }}{{ $isWinner ? ' ✓' : '' }}
                            </span>
                            <span style="color:var(--fg-tertiary);">{{ $count }} ({{ $pct }} %)</span>
                        </div>
                        <div style="background:var(--border-subtle);border-radius:4px;height:8px;overflow:hidden;">
                            <div style="background:{{ $isWinner ? 'var(--mousse-500)' : 'var(--brique-400)' }};height:100%;width:{{ $pct }}%;border-radius:4px;transition:width .3s;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucun vote enregistré.</p>
        @endif

    @elseif ($hasVoted)
        {{-- ====================================================== --}}
        {{-- Déjà voté --}}
        {{-- ====================================================== --}}
        <div style="display:flex;align-items:flex-start;gap:12px;">
            <div style="flex:1;">
                <div style="font-size:14px;font-weight:600;color:var(--fg-primary);margin-bottom:6px;">Vote enregistré</div>
                <p style="font-size:14px;color:var(--fg-secondary);margin:0;">
                    Votre vote a bien été pris en compte. Les résultats seront disponibles après la clôture
                    le {{ $scrutin->closes_at->translatedFormat('j M Y à H\hi') }}.
                </p>
            </div>
        </div>

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

        <form method="POST" action="{{ route('member.scrutins.vote', $scrutin) }}">
            @csrf

            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px;">
                @foreach ($scrutin->options as $option)
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:15px;padding:12px 16px;border:1px solid var(--border-subtle);border-radius:8px;background:var(--surface-default);">
                        <input type="radio" name="scrutin_option_id" value="{{ $option->id }}"
                               {{ (int) old('scrutin_option_id') === $option->id ? 'checked' : '' }} required>
                        {{ $option->label }}
                    </label>
                @endforeach
            </div>

            <button type="submit" class="fb-btn fb-btn-primary"
                    onclick="return confirm('Confirmer votre vote ? Il ne pourra pas être modifié.')">
                Voter
            </button>
        </form>

    @else
        {{-- ====================================================== --}}
        {{-- Pas d'accès --}}
        {{-- ====================================================== --}}
        <p style="font-size:14px;color:var(--fg-tertiary);">
            Ce scrutin n'est pas encore ouvert au vote ou vous n'êtes pas autorisé à voter.
        </p>
    @endif
</div>

{{-- Paramètres du scrutin (info) --}}
<div style="margin-top:20px;font-size:13px;color:var(--fg-tertiary);display:flex;gap:16px;flex-wrap:wrap;">
    <span>Quorum : {{ $scrutin->quorum_type->label() }} — {{ $scrutin->quorum_value }}{{ $scrutin->quorum_type->value === 'proportional' ? ' %' : ' membres' }}</span>
    <span>Majorité : {{ $scrutin->majority_type->label() }}{{ $scrutin->majority_threshold ? ' — seuil '.$scrutin->majority_threshold.' %' : '' }}</span>
</div>

</x-layouts.member>
