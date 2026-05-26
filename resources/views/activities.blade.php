<x-layouts.app title="Nos cercles — La Fabrique">

{{-- ====================================================
     Intro header
     ==================================================== --}}
<section style="background:var(--creme-100);padding:80px 48px;">
    <div style="max-width:1280px;margin:0 auto;">
        <div class="fb-eyebrow">Nos activités</div>
        <h1 class="fb-h1" style="max-width:22ch;margin-top:12px;">Nos cercles thématiques</h1>
        <p class="fb-lead" style="max-width:58ch;margin-top:16px;">
            La Fabrique s'organise en cercles thématiques — des petits groupes autonomes
            qui portent chacun un sujet, de l'idée à la réalisation. Chaque cercle
            a son référent, ses initiatives et son propre rythme de réunion.
        </p>
    </div>
</section>

{{-- ====================================================
     État vide
     ==================================================== --}}
@if ($circles->isEmpty())
<section style="padding:96px 48px;text-align:center;">
    <div style="max-width:480px;margin:0 auto;display:flex;flex-direction:column;align-items:center;gap:20px;">
        <div style="width:64px;height:64px;background:var(--creme-100);border-radius:50%;display:flex;align-items:center;justify-content:center;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--brique-400)" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 3"/>
            </svg>
        </div>
        <h2 class="fb-h3">Les cercles arrivent bientôt</h2>
        <p style="font-size:15px;color:var(--fg-secondary);line-height:1.6;margin:0;">
            Les cercles thématiques sont en cours de constitution.
            Revenez très prochainement pour découvrir nos activités.
        </p>
        <a href="{{ route('inscription') }}" class="fb-btn fb-btn-primary fb-btn-lg">
            Être informé en rejoignant →
        </a>
    </div>
</section>

@else

{{-- ====================================================
     Boucle des cercles
     ==================================================== --}}
@foreach ($circles as $circle)
<article id="cercle-{{ $circle->slug }}"
         style="border-top:3px solid var(--ocre-300);padding:64px 48px;background:{{ $loop->even ? 'var(--bg-page,#fff)' : 'var(--creme-50,#fdfaf3)' }};border-bottom:1px solid var(--border-subtle,#e8e2d5);">
    <div style="max-width:1280px;margin:0 auto;">

        {{-- En-tête du cercle --}}
        <div style="margin-bottom:40px;">
            <div class="fb-eyebrow">Cercle thématique</div>
            <h2 class="fb-h2" style="margin-top:8px;">{{ $circle->name }}</h2>
            @if ($circle->description)
                <p class="fb-lead" style="max-width:64ch;margin-top:12px;">
                    {{ $circle->description }}
                </p>
            @endif
        </div>

        {{-- Grille 3 colonnes --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;align-items:start;">

            {{-- Col 1 — Initiatives en cours --}}
            <div style="background:#fff;border:1px solid var(--border-subtle,#e8e2d5);border-radius:12px;padding:24px;display:flex;flex-direction:column;gap:16px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--brique-500)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                    <h3 style="font:600 13px/1 var(--font-sans,sans-serif);color:var(--fg-primary);margin:0;text-transform:uppercase;letter-spacing:.06em;">
                        Initiatives en cours
                    </h3>
                </div>

                @if ($circle->actions->isEmpty())
                    <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;margin:0;">
                        Aucune initiative en cours pour le moment.
                    </p>
                @else
                    <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:10px;">
                        @foreach ($circle->actions as $action)
                            <li style="display:flex;align-items:flex-start;gap:10px;padding-bottom:10px;{{ ! $loop->last ? 'border-bottom:1px solid var(--border-subtle,#e8e2d5);' : '' }}">
                                <span class="fb-badge {{ $action->status->badgeClass() }}" style="flex-shrink:0;margin-top:1px;">
                                    {{ $action->status->label() }}
                                </span>
                                <span style="font-size:14px;line-height:1.5;color:var(--fg-primary);">
                                    {{ $action->title }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Col 2 — Prochaines réunions publiques --}}
            <div style="background:#fff;border:1px solid var(--border-subtle,#e8e2d5);border-radius:12px;padding:24px;display:flex;flex-direction:column;gap:16px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--mousse-600)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <h3 style="font:600 13px/1 var(--font-sans,sans-serif);color:var(--fg-primary);margin:0;text-transform:uppercase;letter-spacing:.06em;">
                        Prochaines réunions
                    </h3>
                </div>

                @if ($circle->meetings->isEmpty())
                    <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;margin:0;">
                        Aucune réunion publique planifiée.
                    </p>
                @else
                    <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:12px;">
                        @foreach ($circle->meetings as $meeting)
                            <li style="padding-bottom:12px;{{ ! $loop->last ? 'border-bottom:1px solid var(--border-subtle,#e8e2d5);' : '' }}">
                                <div style="font-size:14px;font-weight:600;color:var(--fg-primary);margin-bottom:4px;">
                                    {{ $meeting->title }}
                                </div>
                                <div style="font-size:12px;color:var(--fg-tertiary);font-family:var(--font-mono);">
                                    {{ $meeting->scheduled_at->translatedFormat('D d M Y · H:i') }}
                                </div>
                                @if ($meeting->location)
                                    <div style="font-size:12px;color:var(--fg-secondary);margin-top:3px;">
                                        📍 {{ $meeting->location }}
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Col 3 — Comptes-rendus récents --}}
            <div style="background:#fff;border:1px solid var(--border-subtle,#e8e2d5);border-radius:12px;padding:24px;display:flex;flex-direction:column;gap:16px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--ocre-600)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <h3 style="font:600 13px/1 var(--font-sans,sans-serif);color:var(--fg-primary);margin:0;text-transform:uppercase;letter-spacing:.06em;">
                        Comptes-rendus
                    </h3>
                </div>

                @if ($circle->recentReports->isEmpty())
                    <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;margin:0;">
                        Aucun compte-rendu publié pour le moment.
                    </p>
                @else
                    <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:10px;">
                        @foreach ($circle->recentReports as $report)
                            <li style="padding-bottom:10px;{{ ! $loop->last ? 'border-bottom:1px solid var(--border-subtle,#e8e2d5);' : '' }}">
                                <div style="font-size:14px;font-weight:500;color:var(--fg-primary);margin-bottom:3px;">
                                    {{ $report->meeting->title }}
                                </div>
                                <div style="font-size:12px;color:var(--fg-tertiary);font-family:var(--font-mono);">
                                    @if ($report->published_at)
                                        Publié le {{ $report->published_at->translatedFormat('d M Y') }}
                                    @else
                                        Réunion du {{ $report->meeting->scheduled_at->translatedFormat('d M Y') }}
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>{{-- /grille --}}

        {{-- Ligne meta --}}
        <div style="margin-top:28px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <span class="fb-badge fb-badge-ocre">
                {{ $circle->members_count }}&nbsp;membre{{ $circle->members_count !== 1 ? 's' : '' }}
            </span>
            <span class="fb-badge fb-badge-mousse">
                Cercle depuis {{ $circle->created_at->year }}
            </span>
            @if ($circle->referent)
                <span style="font-size:12px;color:var(--fg-secondary);">
                    Référent·e · <strong>{{ $circle->referent->name }}</strong>
                </span>
            @endif
        </div>

    </div>
</article>
@endforeach

@endif

{{-- ====================================================
     CTA adhésion
     ==================================================== --}}
<section style="background:var(--mousse-700);color:var(--creme-50);padding:96px 48px;position:relative;overflow:hidden;">
    <div style="max-width:1280px;margin:0 auto;display:grid;grid-template-columns:1.4fr 1fr;gap:48px;align-items:center;position:relative;z-index:1;">
        <div>
            <div style="font:600 11px var(--font-sans,sans-serif);letter-spacing:.12em;text-transform:uppercase;color:var(--mousse-300);margin-bottom:16px;">
                Adhérer
            </div>
            <h2 class="fb-h2" style="color:var(--creme-50);">Envie de rejoindre un cercle&nbsp;?</h2>
            <p class="fb-lead" style="color:var(--mousse-100,#c5ddb0);margin-top:16px;max-width:52ch;">
                Pour participer activement aux cercles, prendre part aux réunions et
                contribuer aux initiatives, devenez adhérent·e de La Fabrique.
            </p>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-start;gap:12px;">
            <a href="{{ route('inscription') }}" class="fb-btn fb-btn-primary fb-btn-lg">
                Devenir adhérent·e →
            </a>
            <a href="{{ route('login') }}" style="font-size:14px;color:var(--mousse-200,#a3c98a);text-decoration:none;">
                ou se connecter si déjà adhérent·e
            </a>
        </div>
    </div>
</section>

</x-layouts.app>
