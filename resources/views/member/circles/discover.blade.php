<x-layouts.member title="Rejoindre un cercle — La Fabrique">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Cercles à rejoindre</h1>
        <div class="ea-greeting-sub">Rejoignez les groupes qui vous correspondent.</div>
    </div>
</div>

<div x-data="{ selected: null }" class="circles-layout">

    {{-- Grid --}}
    <div>
        @if (session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        @if ($circles->isEmpty())
            <div style="padding:32px 0;">
                <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;margin:0 0 12px;">Aucun cercle à rejoindre — vous êtes déjà membre ou en attente partout.</p>
                <a href="{{ route('member.circles.index') }}" class="fb-btn fb-btn-ghost fb-btn-sm">Voir mes cercles →</a>
            </div>
        @else
        <div class="circles-grid" :class="{ 'panel-open': selected !== null }">
            @foreach ($circles as $circle)
                @php
                    $isFull = $circle->max_members && $circle->users_count >= $circle->max_members;
                @endphp
                <div
                    class="circle-card {{ $isFull ? 'full' : '' }}"
                    @click="selected = (selected === {{ $circle->id }}) ? null : {{ $circle->id }}"
                    :class="{ 'selected': selected === {{ $circle->id }} }"
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
                                @if ($isFull)
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
                            {{ $circle->users_count }}{{ $circle->max_members ? '/'.$circle->max_members : '' }} membres
                        </span>
                        @if ($isFull)
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
        @endif
    </div>

    {{-- Colonne droite : wrapper pleine hauteur + panneau sticky --}}
    <div class="circle-panel-track">
    <div class="circle-panel" x-show="selected !== null" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-x-4"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-4">
        @foreach ($circles as $circle)
            @php
                $isFull = $circle->max_members && $circle->users_count >= $circle->max_members;
            @endphp
            <div x-show="selected === {{ $circle->id }}">
                <div class="circle-panel-header">
                    <div>
                        <h2 class="circle-panel-title">{{ $circle->name }}</h2>
                        <div style="display:flex;gap:8px;">
                            @if ($isFull)
                                <span class="fb-badge fb-badge-brique">Complet</span>
                            @else
                                <span class="fb-badge fb-badge-mousse">Actif</span>
                            @endif
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

                <div class="circle-panel-action">
                    @if ($isFull)
                        <button class="fb-btn fb-btn-ghost fb-btn-block" disabled>Cercle complet</button>
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
    </div>{{-- /circle-panel-track --}}

</div>

</x-layouts.member>
