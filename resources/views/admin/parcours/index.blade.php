<x-layouts.admin title="Parcours guidé — Admin La Fabrique">

@if (session('success'))
    <div class="flash-success">{{ session('success') }}</div>
@endif

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Administration</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Parcours guidé
        </h1>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.parcours.preview') }}" class="fb-btn fb-btn-ghost fb-btn-sm">Prévisualiser l'arbre</a>
        <a href="{{ route('admin.parcours.questions.create') }}" class="fb-btn fb-btn-primary fb-btn-sm">+ Nouvelle question</a>
        <a href="{{ route('admin.parcours.services.create') }}" class="fb-btn fb-btn-primary fb-btn-sm">+ Nouveau service</a>
    </div>
</div>

{{-- Questions --}}
<div style="margin-bottom:40px;">
    <h2 style="font-size:1rem;font-weight:600;color:var(--fg-secondary);margin-bottom:16px;text-transform:uppercase;letter-spacing:.05em;">
        Questions ({{ $questions->count() }})
    </h2>

    @if ($questions->isEmpty())
        <p style="color:var(--fg-tertiary);font-size:14px;">Aucune question créée. <a href="{{ route('admin.parcours.questions.create') }}" style="color:var(--brique-600);">Créer la première question</a></p>
    @else
        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm);">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Options</th>
                        <th>Racine</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($questions as $question)
                        <tr>
                            <td>
                                <div style="font-weight:500;color:var(--fg-primary);">{{ $question->label }}</div>
                            </td>
                            <td>
                                <div style="font-size:13px;color:var(--fg-tertiary);">{{ $question->options->count() }} option(s)</div>
                                @foreach ($question->options as $opt)
                                    <div style="font-size:12px;color:var(--fg-tertiary);margin-top:2px;">
                                        → {{ $opt->label }}
                                        @if ($opt->service)
                                            <span class="fb-badge fb-badge-mousse" style="font-size:10px;">{{ $opt->service->name }}</span>
                                        @elseif ($opt->nextQuestion)
                                            <span class="fb-badge" style="font-size:10px;">→ question</span>
                                        @else
                                            <span class="fb-badge fb-badge-brique" style="font-size:10px;">non configurée</span>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                @if ($question->is_root)
                                    <span class="fb-badge fb-badge-mousse">Départ</span>
                                @else
                                    <form method="POST" action="{{ route('admin.parcours.questions.set-root', $question) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="fb-btn fb-btn-ghost" style="font-size:11px;padding:2px 8px;">Définir</button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.parcours.questions.edit', $question) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Modifier</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Services --}}
<div>
    <h2 style="font-size:1rem;font-weight:600;color:var(--fg-secondary);margin-bottom:16px;text-transform:uppercase;letter-spacing:.05em;">
        Services ({{ $services->count() }})
    </h2>

    @if ($services->isEmpty())
        <p style="color:var(--fg-tertiary);font-size:14px;">Aucun service créé. <a href="{{ route('admin.parcours.services.create') }}" style="color:var(--brique-600);">Créer le premier service</a></p>
    @else
        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm);">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>CTA</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $service)
                        <tr>
                            <td>
                                <div style="font-weight:500;color:var(--fg-primary);">{{ $service->name }}</div>
                                <div style="font-size:12px;color:var(--fg-tertiary);">{{ Str::limit($service->description, 60) }}</div>
                            </td>
                            <td>
                                <span class="fb-badge">{{ $service->cta_type->label() }}</span>
                            </td>
                            <td>
                                @if ($service->is_active)
                                    <span class="fb-badge fb-badge-mousse">Actif</span>
                                @else
                                    <span class="fb-badge fb-badge-brique">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.parcours.services.edit', $service) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Modifier</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

</x-layouts.admin>
