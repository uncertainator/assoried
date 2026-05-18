<x-layouts.member title="Scrutins — La Fabrique">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Scrutins formels</h1>
        <div class="ea-greeting-sub">Participez aux votes formels de l'association.</div>
    </div>
</div>

@if (session('success'))
    <div style="background:var(--mousse-100);border:1px solid var(--mousse-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--mousse-700);">
        {{ session('success') }}
    </div>
@endif

{{-- Scrutins ouverts --}}
<div style="margin-bottom:40px;">
    <h2 style="font-size:15px;font-weight:600;margin:0 0 16px;color:var(--fg-secondary);">En cours</h2>

    @if ($open->isEmpty())
        <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucun scrutin en cours.</p>
    @else
        <div style="display:flex;flex-direction:column;gap:12px;">
            @foreach ($open as $scrutin)
                <a href="{{ route('member.scrutins.show', $scrutin) }}" style="display:block;text-decoration:none;">
                    <div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                        <div>
                            <div style="font-size:14px;font-weight:600;color:var(--fg-primary);margin-bottom:4px;">{{ $scrutin->title }}</div>
                            <div style="font-size:13px;color:var(--fg-tertiary);">
                                Clôture le {{ $scrutin->closes_at->translatedFormat('j M Y à H\hi') }}
                                &nbsp;·&nbsp;
                                {{ $scrutin->quorum_type->label() }}
                            </div>
                        </div>
                        <span style="background:var(--mousse-100);color:var(--mousse-700);font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;white-space:nowrap;">Ouvert</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

{{-- Scrutins récemment clôturés --}}
<div>
    <h2 style="font-size:15px;font-weight:600;margin:0 0 16px;color:var(--fg-secondary);">Récemment clôturés</h2>

    @if ($recentlyClosed->isEmpty())
        <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucun scrutin clôturé récemment.</p>
    @else
        <div style="display:flex;flex-direction:column;gap:12px;">
            @foreach ($recentlyClosed as $scrutin)
                <a href="{{ route('member.scrutins.show', $scrutin) }}" style="display:block;text-decoration:none;">
                    <div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;opacity:0.8;">
                        <div>
                            <div style="font-size:14px;font-weight:600;color:var(--fg-primary);margin-bottom:4px;">{{ $scrutin->title }}</div>
                            <div style="font-size:13px;color:var(--fg-tertiary);">
                                Clôturé le {{ $scrutin->closes_at->translatedFormat('j M Y') }}
                                &nbsp;·&nbsp;
                                {{ $scrutin->total_votes }} vote(s)
                            </div>
                        </div>
                        @if ($scrutin->result_status)
                            <span class="{{ $scrutin->result_status->badgeClass() }}" style="font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;white-space:nowrap;">
                                {{ $scrutin->result_status->label() }}
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

</x-layouts.member>
