<x-layouts.member :title="$circle->name.' — Annuaire — La Fabrique'">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Annuaire — {{ $circle->name }}</h1>
        @if ($canSeeRoles)
            <div class="ea-greeting-sub">
                {{ $members->count() }} membre{{ $members->count() > 1 ? 's' : '' }}
            </div>
        @endif
    </div>
    <a href="{{ route('member.circles.show', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
        ← Retour au cercle
    </a>
</div>

@if ($members->isEmpty())
    <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">
        Aucun membre dans ce cercle pour le moment.
    </p>
@else
    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach ($members as $member)
            <div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:14px 18px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">

                <div style="width:38px;height:38px;border-radius:50%;background:var(--ocre-100);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:var(--ocre-700);flex-shrink:0;">
                    {{ strtoupper(substr($member->name ?? '?', 0, 2)) }}
                </div>

                <div style="flex:1;min-width:0;">
                    <div style="font-size:15px;font-weight:600;color:var(--fg-primary);">
                        {{ $member->name ?? '(nom non renseigné)' }}
                    </div>

                    @if ($member->consent_display_contact)
                        <div style="font-size:13px;color:var(--fg-secondary);margin-top:2px;">
                            {{ $member->email }}
                        </div>
                    @endif
                </div>

                @if ($canSeeRoles)
                    @if ($circle->referent_id === $member->id)
                        <span class="fb-badge fb-badge-ocre">Référent</span>
                    @else
                        <span class="fb-badge">Adhérent</span>
                    @endif
                @endif

            </div>
        @endforeach
    </div>
@endif

</x-layouts.member>
