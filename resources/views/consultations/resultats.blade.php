<x-layouts.app :title="'Résultats — '.$consultation->titre">

<section class="fb-section" style="padding-top:64px;padding-bottom:64px;">
    <div style="max-width:760px;margin:0 auto;padding:0 24px;">

        <a href="{{ route('consultations.show', $consultation) }}" style="font-size:14px;color:var(--fg-tertiary);text-decoration:none;display:inline-block;margin-bottom:20px;">
            ← Retour à la consultation
        </a>

        <div class="fb-eyebrow" style="margin-bottom:8px;">Résultats</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:0 0 8px;letter-spacing:-.02em;">
            {{ $consultation->titre }}
        </h1>
        <p style="font-size:14px;color:var(--fg-tertiary);margin-bottom:32px;">
            Mode : {{ $consultation->mode_recueil->label() }}
            @if ($consultation->estCloturee())
                · Clôturée le {{ $consultation->date_cloture->translatedFormat('j M Y') }}
            @endif
        </p>

        @if ($consultation->mode_recueil->value === 'vote_indicatif')
            @php $total = array_sum($resultats); @endphp
            <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:10px;padding:24px;margin-bottom:28px;">
                <div style="font-size:14px;font-weight:600;margin-bottom:6px;">{{ $total }} vote(s) exprimé(s)</div>
                <div style="display:flex;flex-direction:column;gap:14px;margin-top:16px;">
                    @foreach ($resultats as $option => $count)
                        @php $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
                        <div>
                            <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:5px;">
                                <span>{{ $option }}</span>
                                <span style="color:var(--fg-tertiary);">{{ $count }} ({{ $pct }} %)</span>
                            </div>
                            <div style="background:var(--border-subtle);border-radius:4px;height:10px;overflow:hidden;">
                                <div style="background:var(--brique-400);height:100%;width:{{ $pct }}%;border-radius:4px;transition:width .3s;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        @elseif ($consultation->mode_recueil->value === 'signature')
            <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:10px;padding:24px;margin-bottom:28px;text-align:center;">
                <div style="font-size:48px;font-weight:700;color:var(--fg-primary);font-family:var(--font-display);">{{ $resultats }}</div>
                <div style="font-size:16px;color:var(--fg-secondary);margin-top:8px;">signature(s) recueillie(s)</div>
            </div>

        @elseif ($consultation->mode_recueil->value === 'avis_libre')
            <div style="margin-bottom:12px;font-size:14px;color:var(--fg-tertiary);">
                {{ $avisLibres->total() }} avis public(s)
            </div>
            @if ($avisLibres->isEmpty())
                <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucun avis public pour le moment.</p>
            @else
                <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px;">
                    @foreach ($avisLibres as $avis)
                        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:8px;padding:16px 20px;">
                            <p style="margin:0 0 8px;font-size:15px;line-height:1.6;color:var(--fg-primary);">{{ $avis->contenu }}</p>
                            <div style="font-size:12px;color:var(--fg-tertiary);">{{ $avis->created_at->translatedFormat('j M Y') }}</div>
                        </div>
                    @endforeach
                </div>
                {{ $avisLibres->links() }}
            @endif
        @endif

    </div>
</section>

</x-layouts.app>
