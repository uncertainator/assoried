<x-layouts.member title="Sondages — La Fabrique">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Sondages</h1>
        <div class="ea-greeting-sub">Consultez et participez aux sondages de vos cercles et de l'association.</div>
    </div>
    @if (auth()->user()->isAdmin())
        <a href="{{ route('member.polls.create') }}" class="fb-btn fb-btn-primary fb-btn-sm">
            + Nouveau sondage association
        </a>
    @endif
</div>

@if (session('success'))
    <div style="background:var(--mousse-100);border:1px solid var(--mousse-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--mousse-700);">
        {{ session('success') }}
    </div>
@endif

@php
    $open   = $polls->filter(fn($p) => ! $p->isClosed());
    $closed = $polls->filter(fn($p) => $p->isClosed());
@endphp

{{-- Sondages en cours --}}
<div style="margin-bottom:40px;">
    <h2 style="font-size:15px;font-weight:600;margin:0 0 16px;color:var(--fg-secondary);">En cours</h2>

    @if ($open->isEmpty())
        <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucun sondage en cours.</p>
    @else
        <div style="display:flex;flex-direction:column;gap:12px;">
            @foreach ($open as $poll)
                <a href="{{ route('member.polls.show', $poll) }}" style="display:block;text-decoration:none;">
                    <div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                        <div>
                            <div style="font-size:14px;font-weight:600;color:var(--fg-primary);margin-bottom:4px;">{{ $poll->title }}</div>
                            <div style="font-size:13px;color:var(--fg-tertiary);">
                                {{ $poll->circle?->name ?? 'Association' }}
                                &nbsp;·&nbsp;
                                {{ $poll->type->label() }}
                                &nbsp;·&nbsp;
                                Clôture le {{ $poll->closes_at->translatedFormat('j M Y à H\hi') }}
                            </div>
                        </div>
                        <span style="background:var(--mousse-100);color:var(--mousse-700);font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;white-space:nowrap;">En cours</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

{{-- Sondages clôturés --}}
<div>
    <h2 style="font-size:15px;font-weight:600;margin:0 0 16px;color:var(--fg-secondary);">Clôturés</h2>

    @if ($closed->isEmpty())
        <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucun sondage clôturé.</p>
    @else
        <div style="display:flex;flex-direction:column;gap:12px;">
            @foreach ($closed as $poll)
                <a href="{{ route('member.polls.show', $poll) }}" style="display:block;text-decoration:none;">
                    <div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;opacity:0.75;">
                        <div>
                            <div style="font-size:14px;font-weight:600;color:var(--fg-primary);margin-bottom:4px;">{{ $poll->title }}</div>
                            <div style="font-size:13px;color:var(--fg-tertiary);">
                                {{ $poll->circle?->name ?? 'Association' }}
                                &nbsp;·&nbsp;
                                {{ $poll->type->label() }}
                                &nbsp;·&nbsp;
                                Clôturé le {{ $poll->closes_at->translatedFormat('j M Y') }}
                                &nbsp;·&nbsp;
                                {{ $poll->votes()->count() }} {{ Str::plural('vote', $poll->votes()->count()) }}
                            </div>
                        </div>
                        <span style="background:var(--surface-subtle);color:var(--fg-tertiary);font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;white-space:nowrap;">Clôturé</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

</x-layouts.member>
