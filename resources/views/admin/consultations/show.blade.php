<x-layouts.admin :title="$consultation->titre.' — Admin'">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Consultation publique</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            {{ $consultation->titre }}
        </h1>
        <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
            <span style="font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;{{ $consultation->estOuverte() ? 'background:var(--mousse-100);color:var(--mousse-700)' : 'background:var(--bg-surface-3);color:var(--fg-tertiary)' }}">
                {{ $consultation->estOuverte() ? 'Ouverte' : 'Clôturée' }}
            </span>
            <span style="font-size:12px;padding:3px 10px;border-radius:20px;background:var(--bg-surface-2);color:var(--fg-tertiary);">
                {{ $consultation->mode_recueil->label() }}
            </span>
        </div>
    </div>
    <a href="{{ route('admin.consultations.index') }}" class="fb-btn fb-btn-ghost fb-btn-sm">← Retour</a>
</div>

@if (session('success'))
    <div style="background:var(--mousse-100);border:1px solid var(--mousse-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--mousse-700);">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div style="background:var(--brique-100);border:1px solid var(--brique-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--brique-700);">
        @foreach ($errors->all() as $error) <div>{{ $error }}</div> @endforeach
    </div>
@endif

{{-- Actions --}}
<div style="display:flex;gap:10px;margin-bottom:24px;flex-wrap:wrap;">
    @if ($consultation->estOuverte())
        <a href="{{ route('admin.consultations.edit', $consultation) }}" class="fb-btn fb-btn-outline fb-btn-sm">Modifier</a>
        <form method="POST" action="{{ route('admin.consultations.cloturer', $consultation) }}">
            @csrf
            <button type="submit" class="fb-btn fb-btn-outline fb-btn-sm"
                    onclick="return confirm('Clôturer manuellement cette consultation ?')">
                Clôturer maintenant
            </button>
        </form>
    @endif
    <a href="{{ route('admin.consultations.terrain', $consultation) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Saisir terrain</a>
    <a href="{{ route('consultations.terrain.print', $consultation) }}" target="_blank" class="fb-btn fb-btn-ghost fb-btn-sm">Imprimer fiche terrain</a>
    <a href="{{ route('consultations.show', $consultation) }}" target="_blank" class="fb-btn fb-btn-ghost fb-btn-sm">Vue publique ↗</a>
</div>

{{-- Métadonnées --}}
<div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:10px;padding:20px;margin-bottom:24px;">
    <dl style="display:grid;grid-template-columns:1fr 1fr;gap:12px 24px;font-size:14px;margin:0;">
        @if ($consultation->description)
            <div style="grid-column:span 2;">
                <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Description</dt>
                <dd style="margin:0;">{{ $consultation->description }}</dd>
            </div>
        @endif
        <div>
            <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Clôture</dt>
            <dd style="margin:0;">{{ $consultation->date_cloture?->translatedFormat('j M Y à H\hi') ?? 'Pas de date définie' }}</dd>
        </div>
        <div>
            <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Réponses totales</dt>
            <dd style="margin:0;font-weight:600;">{{ $reponses->total() }}</dd>
        </div>
        @if ($consultation->mode_recueil->value === 'vote_indicatif' && $consultation->options)
            <div style="grid-column:span 2;">
                <dt style="color:var(--fg-tertiary);font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Options</dt>
                <dd style="margin:0;display:flex;gap:6px;flex-wrap:wrap;">
                    @foreach ($consultation->options as $opt)
                        <span style="background:var(--bg-surface-3);padding:2px 8px;border-radius:4px;font-size:13px;">{{ $opt }}</span>
                    @endforeach
                </dd>
            </div>
        @endif
    </dl>
</div>

{{-- Liste des réponses --}}
<div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:10px;overflow:hidden;">
    <div style="padding:16px 20px;font-size:14px;font-weight:600;border-bottom:1px solid var(--border-subtle);">
        Réponses ({{ $reponses->total() }})
    </div>

    @if ($reponses->isEmpty())
        <p style="padding:20px;font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucune réponse enregistrée.</p>
    @else
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-subtle);">
                    <th style="text-align:left;padding:10px 16px;font-weight:500;color:var(--fg-tertiary);">Contenu</th>
                    <th style="text-align:left;padding:10px 16px;font-weight:500;color:var(--fg-tertiary);">Source</th>
                    <th style="text-align:left;padding:10px 16px;font-weight:500;color:var(--fg-tertiary);">Date</th>
                    <th style="text-align:left;padding:10px 16px;font-weight:500;color:var(--fg-tertiary);">Statut</th>
                    <th style="text-align:right;padding:10px 16px;font-weight:500;color:var(--fg-tertiary);">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reponses as $reponse)
                    <tr style="border-bottom:1px solid var(--border-subtle);{{ $reponse->masque ? 'opacity:.55;' : '' }}">
                        <td style="padding:10px 16px;max-width:320px;">
                            @if ($reponse->mode === 'signature')
                                @php $sig = $reponse->signatureArray(); @endphp
                                {{ $sig['prenom'] ?? '' }} {{ $sig['nom'] ?? '' }}
                            @else
                                <span style="word-break:break-word;">{{ $reponse->contenu }}</span>
                            @endif
                            @if ($reponse->ip_address)
                                <details style="margin-top:4px;">
                                    <summary style="font-size:11px;color:var(--fg-tertiary);cursor:pointer;">IP</summary>
                                    <span style="font-size:11px;font-family:monospace;color:var(--fg-tertiary);">{{ $reponse->ip_address }}</span>
                                </details>
                            @endif
                        </td>
                        <td style="padding:10px 16px;color:var(--fg-tertiary);">
                            {{ $reponse->source->label() }}
                        </td>
                        <td style="padding:10px 16px;color:var(--fg-tertiary);white-space:nowrap;">
                            {{ $reponse->created_at->translatedFormat('j M Y') }}
                        </td>
                        <td style="padding:10px 16px;">
                            @if ($reponse->masque)
                                <span style="background:var(--brique-100);color:var(--brique-700);font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;">masquée</span>
                            @else
                                <span style="background:var(--mousse-100);color:var(--mousse-700);font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;">visible</span>
                            @endif
                        </td>
                        <td style="padding:10px 16px;text-align:right;">
                            @if ($reponse->masque)
                                <form method="POST" action="{{ route('admin.consultations.reponses.demasquer', $reponse) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm">Démasquer</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.consultations.reponses.masquer', $reponse) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm" style="color:var(--brique-600);"
                                            onclick="return confirm('Masquer cet avis ?')">Masquer</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:16px 20px;">{{ $reponses->links() }}</div>
    @endif
</div>

</x-layouts.admin>
