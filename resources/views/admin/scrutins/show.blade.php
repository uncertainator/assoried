<x-layouts.admin :title="$scrutin->title.' — Admin'">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Scrutin</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            {{ $scrutin->title }}
        </h1>
        <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
            <span class="{{ $scrutin->status->badgeClass() }}" style="font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;">
                {{ $scrutin->status->label() }}
            </span>
            @if ($scrutin->isClosed() && $scrutin->result_status)
                <span class="{{ $scrutin->result_status->badgeClass() }}" style="font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;">
                    {{ $scrutin->result_status->label() }}
                </span>
            @endif
        </div>
    </div>
    <a href="{{ route('admin.scrutins.index') }}" class="fb-btn fb-btn-ghost fb-btn-sm">← Retour</a>
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

{{-- Actions d'état --}}
@if ($scrutin->isEditable())
    <div style="display:flex;gap:10px;margin-bottom:24px;flex-wrap:wrap;">
        <a href="{{ route('admin.scrutins.edit', $scrutin) }}" class="fb-btn fb-btn-outline fb-btn-sm">Modifier</a>
        <form method="POST" action="{{ route('admin.scrutins.publish', $scrutin) }}">
            @csrf
            <button type="submit" class="fb-btn fb-btn-primary fb-btn-sm"
                    onclick="return confirm('Publier ce scrutin ? Il ne pourra plus être modifié.')">
                Publier
            </button>
        </form>
        <form method="POST" action="{{ route('admin.scrutins.cancel', $scrutin) }}">
            @csrf
            <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm"
                    onclick="return confirm('Annuler définitivement ce scrutin ?')" style="color:var(--brique-600);">
                Annuler le scrutin
            </button>
        </form>
    </div>
@elseif ($scrutin->isOpen())
    <div style="display:flex;gap:10px;margin-bottom:24px;flex-wrap:wrap;">
        <form method="POST" action="{{ route('admin.scrutins.close', $scrutin) }}">
            @csrf
            <button type="submit" class="fb-btn fb-btn-outline fb-btn-sm"
                    onclick="return confirm('Clôturer manuellement ce scrutin ? Les résultats seront calculés immédiatement.')">
                Clôturer maintenant
            </button>
        </form>
        @if ($scrutin->canBeCancelled())
            <form method="POST" action="{{ route('admin.scrutins.cancel', $scrutin) }}">
                @csrf
                <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm"
                        onclick="return confirm('Annuler ce scrutin ? Aucun vote n\'a été enregistré.')" style="color:var(--brique-600);">
                    Annuler le scrutin
                </button>
            </form>
        @endif
    </div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">

    {{-- Métadonnées --}}
    <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:10px;padding:20px;">
        <div style="font-size:14px;font-weight:600;margin-bottom:16px;">Paramètres</div>
        <dl style="display:flex;flex-direction:column;gap:10px;font-size:14px;margin:0;">
            @if ($scrutin->description)
                <div>
                    <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Description</dt>
                    <dd style="margin:0;color:var(--fg-primary);">{{ $scrutin->description }}</dd>
                </div>
            @endif
            <div>
                <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Ouverture</dt>
                <dd style="margin:0;">{{ $scrutin->opened_at?->translatedFormat('j M Y à H\hi') ?? '—' }}</dd>
            </div>
            <div>
                <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Clôture</dt>
                <dd style="margin:0;">{{ $scrutin->closes_at?->translatedFormat('j M Y à H\hi') ?? '—' }}</dd>
            </div>
            <div>
                <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Quorum</dt>
                <dd style="margin:0;">{{ $scrutin->quorum_type->label() }} — {{ $scrutin->quorum_value }}{{ $scrutin->quorum_type->value === 'proportional' ? ' %' : ' membres' }}</dd>
            </div>
            <div>
                <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Majorité</dt>
                <dd style="margin:0;">
                    {{ $scrutin->majority_type->label() }}
                    @if ($scrutin->majority_threshold)
                        — seuil {{ $scrutin->majority_threshold }} %
                    @endif
                </dd>
            </div>
            <div>
                <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Créé par</dt>
                <dd style="margin:0;">{{ $scrutin->creator->name }}</dd>
            </div>
        </dl>
    </div>

    {{-- Résultats (si clôturé) --}}
    @if ($scrutin->isClosed())
        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:10px;padding:20px;">
            <div style="font-size:14px;font-weight:600;margin-bottom:16px;">Résultats</div>
            <dl style="display:flex;flex-direction:column;gap:10px;font-size:14px;margin:0 0 16px;">
                <div>
                    <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Votes exprimés</dt>
                    <dd style="margin:0;font-size:22px;font-weight:700;color:var(--fg-primary);">{{ $scrutin->total_votes }}</dd>
                </div>
                @if ($scrutin->active_members_at_close)
                    <div>
                        <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Membres actifs (à la clôture)</dt>
                        <dd style="margin:0;">{{ $scrutin->active_members_at_close }}
                            @if ($scrutin->total_votes && $scrutin->active_members_at_close > 0)
                                <span style="color:var(--fg-tertiary);">({{ round($scrutin->total_votes / $scrutin->active_members_at_close * 100, 1) }} % participation)</span>
                            @endif
                        </dd>
                    </div>
                @endif
                @if ($scrutin->winningOption)
                    <div>
                        <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Option adoptée</dt>
                        <dd style="margin:0;font-weight:600;color:var(--mousse-700);">{{ $scrutin->winningOption->label }}</dd>
                    </div>
                @endif
            </dl>

            {{-- Répartition par option --}}
            @php
                $totalVotes = $scrutin->total_votes ?: 1;
                $voteCounts = $scrutin->votes->groupBy('scrutin_option_id')->map->count();
            @endphp
            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach ($scrutin->options as $option)
                    @php $count = $voteCounts[$option->id] ?? 0; $pct = round($count / $totalVotes * 100); @endphp
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:4px;">
                            <span style="font-weight:{{ $scrutin->winningOption?->id === $option->id ? '700' : '400' }};">{{ $option->label }}</span>
                            <span style="color:var(--fg-tertiary);">{{ $count }} ({{ $pct }} %)</span>
                        </div>
                        <div style="background:var(--border-subtle);border-radius:4px;height:8px;overflow:hidden;">
                            <div style="background:var(--brique-400);height:100%;width:{{ $pct }}%;border-radius:4px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- Options listées --}}
        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:10px;padding:20px;">
            <div style="font-size:14px;font-weight:600;margin-bottom:16px;">Options de vote</div>
            <ol style="margin:0;padding-left:20px;display:flex;flex-direction:column;gap:8px;">
                @foreach ($scrutin->options as $option)
                    <li style="font-size:14px;">{{ $option->label }}</li>
                @endforeach
            </ol>
            @if ($scrutin->isOpen())
                <div style="margin-top:16px;font-size:13px;color:var(--fg-tertiary);">
                    {{ $scrutin->votes->count() }} vote(s) enregistré(s)
                </div>
            @endif
        </div>
    @endif
</div>

{{-- Liste des votes (admin uniquement) --}}
@if ($scrutin->votes->isNotEmpty())
    <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:10px;overflow:hidden;">
        <div style="padding:16px 20px;font-size:14px;font-weight:600;border-bottom:1px solid var(--border-subtle);">
            Détail des votes ({{ $scrutin->votes->count() }})
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-subtle);">
                    <th style="text-align:left;padding:10px 16px;font-weight:500;color:var(--fg-tertiary);">Membre</th>
                    <th style="text-align:left;padding:10px 16px;font-weight:500;color:var(--fg-tertiary);">Option choisie</th>
                    <th style="text-align:left;padding:10px 16px;font-weight:500;color:var(--fg-tertiary);">Date du vote</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($scrutin->votes as $vote)
                    <tr style="border-bottom:1px solid var(--border-subtle);">
                        <td style="padding:10px 16px;">{{ $vote->user->name }}</td>
                        <td style="padding:10px 16px;">{{ $vote->option->label }}</td>
                        <td style="padding:10px 16px;color:var(--fg-tertiary);">{{ $vote->created_at->translatedFormat('j M Y à H\hi') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

</x-layouts.admin>
