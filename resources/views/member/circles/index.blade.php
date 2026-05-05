<x-layouts.member title="Mes cercles — La Fabrique">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Cercles thématiques</h1>
        <div class="ea-greeting-sub">Rejoignez les groupes qui vous correspondent.</div>
    </div>
</div>

@php
    $myCircleIds = $myCircleIds->toArray();
@endphp

<div x-data="{ selected: null }" style="display:grid;grid-template-columns:1fr;gap:0;">

    {{-- Grid --}}
    <div>
        @if (session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        <div class="circles-grid">
            @foreach ($circles as $circle)
                @php
                    $isMember = in_array($circle->id, $myCircleIds);
                    $isFull   = $circle->max_members && $circle->users_count >= $circle->max_members && !$isMember;
                @endphp
                <div
                    class="circle-card {{ $isMember ? 'selected' : '' }} {{ $isFull ? 'full' : '' }}"
                    @click="selected = (selected === {{ $circle->id }}) ? null : {{ $circle->id }}"
                    :class="{ 'selected': selected === {{ $circle->id }} || {{ $isMember ? 'true' : 'false' }} }"
                >
                    <div class="circle-card-header">
                        <div class="circle-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brique-500)" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"/><path d="M8 12h8M12 8v8"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="circle-name">{{ $circle->name }}</h3>
                            <div class="circle-badge-row">
                                @if ($isMember)
                                    <span class="fb-badge fb-badge-mousse">✓ Sélectionné</span>
                                @elseif ($isFull)
                                    <span class="fb-badge fb-badge-brique">Complet</span>
                                @else
                                    <span class="fb-badge fb-badge-ocre">Actif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <p class="circle-desc">{{ Str::limit($circle->description, 100) }}</p>
                    <div class="circle-footer">
                        <span class="circle-members">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                            {{ $circle->users_count }}{{ $circle->max_members ? '/' . $circle->max_members : '' }} membres
                        </span>
                        @if ($isMember)
                            <form method="POST" action="{{ route('member.circles.leave', $circle) }}" @click.stop>
                                @csrf @method('DELETE')
                                <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm"
                                        onclick="return confirm('Quitter ce cercle ?')">Quitter</button>
                            </form>
                        @elseif ($isFull)
                            <button class="fb-btn fb-btn-ghost fb-btn-sm" disabled>Complet</button>
                        @else
                            <form method="POST" action="{{ route('member.circles.join', $circle) }}" @click.stop>
                                @csrf
                                <button type="submit" class="fb-btn fb-btn-primary fb-btn-sm">Rejoindre</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Detail panel (Alpine.js) --}}
    <div class="circle-panel" x-show="selected !== null" x-cloak
         style="margin-top:24px;border-top:1px solid var(--border-subtle);padding:24px 0 0;">
        @foreach ($circles as $circle)
            <div x-show="selected === {{ $circle->id }}">
                <div class="circle-panel-header">
                    <div>
                        <h2 class="circle-panel-title">{{ $circle->name }}</h2>
                        <div style="display:flex;gap:8px;">
                            <span class="fb-badge fb-badge-mousse">Actif</span>
                            <span class="fb-badge fb-badge-ocre">{{ $circle->users_count }} membres</span>
                        </div>
                    </div>
                    <button @click="selected = null" style="background:none;border:none;cursor:pointer;color:var(--fg-tertiary);font-size:20px;line-height:1;">&times;</button>
                </div>

                <div class="circle-panel-section">
                    <div class="circle-panel-section-head">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                        Mission
                    </div>
                    <p style="font-size:14px;line-height:1.6;color:var(--fg-secondary);margin:0;">{{ $circle->description }}</p>
                </div>

                <div class="circle-panel-section">
                    <div class="circle-panel-section-head">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/></svg>
                        Prochaines actions
                    </div>
                    <p style="font-size:13px;color:var(--fg-tertiary);font-style:italic;margin:0;">
                        Le programme de ce cercle sera publié prochainement.
                    </p>
                </div>

                <div class="circle-panel-action">
                    @php $isMem = in_array($circle->id, $myCircleIds); @endphp
                    @if ($isMem)
                        <form method="POST" action="{{ route('member.circles.leave', $circle) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="fb-btn fb-btn-outline fb-btn-block"
                                    onclick="return confirm('Quitter ce cercle ?')">
                                Quitter ce cercle
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('member.circles.join', $circle) }}">
                            @csrf
                            <button type="submit" class="fb-btn fb-btn-primary fb-btn-block">
                                Rejoindre ce cercle →
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

</div>

</x-layouts.member>
