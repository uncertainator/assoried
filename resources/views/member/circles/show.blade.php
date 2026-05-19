<x-layouts.member :title="$circle->name.' — La Fabrique'">

{{-- En-tête --}}
<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">{{ $circle->name }}</h1>
        @if ($circle->description)
            <div class="ea-greeting-sub">{{ $circle->description }}</div>
        @endif
        @if ($circle->referent)
            <div style="font-size:13px;color:var(--fg-tertiary);margin-top:4px;">
                Référent : {{ $circle->referent->name }}
            </div>
        @endif
    </div>
    <a href="{{ route('member.circles.index') }}" class="fb-btn fb-btn-ghost fb-btn-sm">
        ← Tous les cercles
    </a>
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

{{-- Grille deux colonnes --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">

    {{-- ===== Colonne gauche : Publications ===== --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Nouvelle publication --}}
        @can('create', [App\Models\Post::class, $circle])
        <div class="fb-hero-card">
            <h2 style="font-size:15px;font-weight:600;margin:0 0 16px;color:var(--fg-primary);">Nouvelle publication</h2>
            <form method="POST" action="{{ route('member.circles.posts.store', $circle) }}">
                @csrf
                <div style="margin-bottom:12px;">
                    <textarea
                        name="body"
                        rows="5"
                        maxlength="5000"
                        placeholder="Rédigez votre publication…"
                        style="width:100%;padding:10px 12px;border:1px solid var(--border-subtle);border-radius:6px;font-size:14px;resize:vertical;background:var(--surface-default);color:var(--fg-primary);box-sizing:border-box;"
                        required
                    >{{ old('body') }}</textarea>
                    @error('body')
                        <div style="color:var(--brique-500);font-size:13px;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;">
                        <input type="checkbox" name="push_to_general" value="1" {{ old('push_to_general') ? 'checked' : '' }}>
                        Pousser dans les Publications générales
                    </label>
                    <button type="submit" class="fb-btn fb-btn-primary fb-btn-sm">Publier</button>
                </div>
            </form>
        </div>
        @endcan

        {{-- Fil des publications --}}
        <div class="fb-hero-card">
            <h2 style="font-size:15px;font-weight:600;margin:0 0 16px;color:var(--fg-primary);">Fil d'actualité</h2>

            @if ($posts->isEmpty())
                <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">
                    Aucune publication pour le moment.
                </p>
            @else
                <div style="display:flex;flex-direction:column;gap:0;">
                    @foreach ($posts as $post)
                        <div style="padding:16px 0;{{ !$loop->last ? 'border-bottom:1px solid var(--border-subtle);' : '' }}">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:10px;flex-wrap:wrap;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:32px;height:32px;border-radius:50%;background:var(--brique-100);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:var(--brique-700);flex-shrink:0;">
                                        {{ strtoupper(substr($post->author?->name ?: '?', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-size:14px;font-weight:500;">{{ $post->author?->name ?? 'Auteur supprimé' }}</div>
                                        <div style="font-size:12px;color:var(--fg-tertiary);">{{ $post->created_at->translatedFormat('d M Y à H:i') }}</div>
                                    </div>
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                    @if ($post->pushed_to_general)
                                        <span class="fb-badge fb-badge-mousse">Dans les Publications générales</span>
                                    @endif
                                    @if ($circle->isManagedBy(auth()->user()))
                                        @unless ($post->pushed_to_general)
                                            <form method="POST" action="{{ route('member.posts.push', $post) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm">
                                                    Pousser dans les Publications générales
                                                </button>
                                            </form>
                                        @endunless
                                        <form method="POST" action="{{ route('member.posts.destroy', $post) }}"
                                              onsubmit="return confirm('Supprimer cette publication ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm" style="color:var(--brique-500);">
                                                Supprimer
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div style="font-size:14px;line-height:1.7;color:var(--fg-primary);white-space:pre-wrap;">{{ $post->body }}</div>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top:20px;">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="fb-hero-card">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;">
                <h2 style="font-size:15px;font-weight:600;margin:0;color:var(--fg-primary);">Actions portées par le cercle</h2>
                @can('create', [App\Models\CircleAction::class, $circle])
                    <button x-data @click="$dispatch('open-action-form')"
                            class="fb-btn fb-btn-primary fb-btn-sm">+ Nouvelle action</button>
                @endcan
            </div>


            @if ($actions->isEmpty())
                <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucune action pour ce cercle.</p>
            @else
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach ($actions as $action)
                        <div style="background:var(--surface-default);border:1px solid var(--border-subtle);border-radius:8px;padding:14px 18px;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                <div style="flex:1;min-width:0;">
                                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                                        <span style="font-size:14px;font-weight:600;">{{ $action->title }}</span>
                                        <span class="fb-badge {{ $action->status->badgeClass() }}">{{ $action->status->label() }}</span>
                                    </div>
                                    <div style="font-size:13px;color:var(--fg-secondary);">
                                        Échéance : {{ $action->due_date->translatedFormat('d F Y') }}
                                    </div>
                                    @if ($action->description)
                                        <div style="font-size:13px;color:var(--fg-tertiary);margin-top:4px;">{{ $action->description }}</div>
                                    @endif
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;flex-wrap:wrap;">
                                    @can('update', $action)
                                        <form method="POST" action="{{ route('member.circle.actions.update', $action) }}" style="display:flex;align-items:center;gap:6px;">
                                            @csrf @method('PATCH')
                                            <select name="status"
                                                    style="font-size:13px;padding:4px 8px;border:1px solid var(--border-subtle);border-radius:6px;background:var(--surface-default);color:var(--fg-primary);">
                                                @foreach (App\Enums\CircleActionStatus::cases() as $s)
                                                    <option value="{{ $s->value }}" {{ $action->status === $s ? 'selected' : '' }}>
                                                        {{ $s->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm">Sauvegarder</button>
                                        </form>
                                    @endcan
                                    @can('delete', $action)
                                        <form method="POST" action="{{ route('member.circle.actions.destroy', $action) }}"
                                              onsubmit="return confirm('Supprimer cette action ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm" style="color:var(--brique-500);">Supprimer</button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ===== Colonne droite : Cartes ===== --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Événements --}}
        <div class="fb-hero-card">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;">
                <h2 style="font-size:15px;font-weight:600;margin:0;color:var(--fg-primary);">Événements</h2>
                @can('create', [App\Models\Event::class, $circle])
                    <a href="{{ route('member.agenda.create', $circle) }}" class="fb-btn fb-btn-primary fb-btn-sm">+ Nouvel événement</a>
                @endcan
            </div>

            @if ($upcomingEvents->isEmpty())
                <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucun événement à venir.</p>
            @else
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach ($upcomingEvents as $event)
                        @include('member.agenda._event-row', ['event' => $event, 'showCircle' => false])
                    @endforeach
                </div>
            @endif

            @if ($pastEvents->isNotEmpty())
                <div x-data="{ open: false }" style="margin-top:12px;">
                    <button @click="open = !open"
                            style="font-size:13px;color:var(--fg-tertiary);background:none;border:none;cursor:pointer;padding:0;display:flex;align-items:center;gap:6px;">
                        <span x-text="open ? '▲' : '▼'">▼</span>
                        Événements passés ({{ $pastEvents->count() }})
                    </button>
                    <div x-show="open" x-cloak style="display:flex;flex-direction:column;gap:10px;margin-top:10px;">
                        @foreach ($pastEvents as $event)
                            @include('member.agenda._event-row', ['event' => $event, 'showCircle' => false, 'isPast' => true])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Réunions --}}
        @can('viewAny', [App\Models\Meeting::class, $circle])
        <div class="fb-hero-card">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px;">
                <h2 style="font-size:15px;font-weight:600;margin:0;color:var(--fg-primary);">Réunions</h2>
                <div style="display:flex;align-items:center;gap:8px;">
                    @can('create', [App\Models\Meeting::class, $circle])
                        <a href="{{ route('member.meetings.create', $circle) }}" class="fb-btn fb-btn-primary fb-btn-sm">+ Nouvelle réunion</a>
                    @endcan
                    <a href="{{ route('member.circles.meetings.index', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Voir les réunions →</a>
                </div>
            </div>
            <p style="font-size:14px;color:var(--fg-tertiary);margin:0;">
                Retrouvez l'ordre du jour et les comptes-rendus des réunions du cercle.
            </p>
        </div>
        @endcan

        {{-- Bibliothèque de ressources --}}
        <div class="fb-hero-card">
            <h2 style="font-size:15px;font-weight:600;margin:0 0 16px;color:var(--fg-primary);">Bibliothèque de ressources</h2>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">

                {{-- Journal de bord --}}
                <div style="background:var(--surface-default);border:1px solid var(--border-subtle);border-radius:8px;padding:16px;display:flex;flex-direction:column;gap:8px;">
                    <h3 style="font-size:14px;font-weight:600;margin:0 0 4px;color:var(--fg-primary);">Journal de bord</h3>
                    @can('create', [App\Models\CircleJournalEntry::class, $circle])
                        <a href="{{ route('member.circles.journal.create', $circle) }}" class="fb-btn fb-btn-primary fb-btn-sm">
                            + Nouvelle entrée
                        </a>
                    @endcan
                    <a href="{{ route('member.circles.journal.index', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
                        Voir le journal →
                    </a>
                </div>

                {{-- Bibliothèque de documents --}}
                <div style="background:var(--surface-default);border:1px solid var(--border-subtle);border-radius:8px;padding:16px;display:flex;flex-direction:column;gap:8px;">
                    <h3 style="font-size:14px;font-weight:600;margin:0 0 4px;color:var(--fg-primary);">Bibliothèque</h3>
                    @can('create', [App\Models\CircleDocument::class, $circle])
                        <a href="{{ route('referent.circle.documents.create', $circle) }}" class="fb-btn fb-btn-primary fb-btn-sm">
                            + Ajouter un document
                        </a>
                    @endcan
                    <a href="{{ route('member.circles.documents.index', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
                        Voir les documents →
                    </a>
                </div>

            </div>
        </div>

        {{-- Annuaire --}}
        <div class="fb-hero-card">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <h2 style="font-size:15px;font-weight:600;margin:0;color:var(--fg-primary);">Annuaire</h2>
                <a href="{{ route('member.circles.directory', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
                    Voir l'annuaire →
                </a>
            </div>
        </div>

        {{-- Quitter le cercle --}}
        @if ($membership && ! $circle->isManagedBy(auth()->user()))
        <div>
            <button @click="$dispatch('open-leave-circle')"
                    class="fb-btn fb-btn-ghost fb-btn-sm"
                    style="color:var(--brique-500);width:100%;">
                Quitter ce cercle
            </button>
        </div>
        @endif

    </div>

</div>

{{-- Modaux téléportés dans <body> via @push/@stack (avant </x-layouts.member>) --}}

@can('create', [App\Models\CircleAction::class, $circle])
@push('modals')
<div x-data="{ open: false }" @open-action-form.window="open = true">
    <template x-if="open">
        <div style="display:flex;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:100;align-items:center;justify-content:center;padding:20px;">
            <div style="background:#fff;border-radius:12px;padding:28px 32px;width:100%;max-width:520px;box-shadow:0 8px 32px rgba(0,0,0,0.15);">
                <h3 style="font-size:16px;font-weight:600;margin:0 0 20px;">Nouvelle action</h3>
                <form method="POST" action="{{ route('member.circle.actions.store', $circle) }}">
                    @csrf
                    <div style="margin-bottom:14px;">
                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Titre *</label>
                        <input type="text" name="title" value="{{ old('title') }}" maxlength="150" required
                               style="width:100%;padding:8px 12px;border:1px solid var(--border-subtle);border-radius:6px;font-size:14px;background:var(--surface-raised);color:var(--fg-primary);box-sizing:border-box;">
                        @error('title')
                            <div style="color:var(--brique-500);font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Description</label>
                        <textarea name="description" rows="3"
                                  style="width:100%;padding:8px 12px;border:1px solid var(--border-subtle);border-radius:6px;font-size:14px;resize:vertical;background:var(--surface-raised);color:var(--fg-primary);box-sizing:border-box;">{{ old('description') }}</textarea>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Date d'échéance *</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" required
                               style="padding:8px 12px;border:1px solid var(--border-subtle);border-radius:6px;font-size:14px;background:var(--surface-raised);color:var(--fg-primary);">
                        @error('due_date')
                            <div style="color:var(--brique-500);font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" @click="open = false" class="fb-btn fb-btn-ghost fb-btn-sm">Annuler</button>
                        <button type="submit" class="fb-btn fb-btn-primary fb-btn-sm">Créer l'action</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endpush
@endcan

@if ($membership && ! $circle->isManagedBy(auth()->user()))
@push('modals')
<div x-data="{ open: false }" @open-leave-circle.window="open = true">
    <template x-if="open">
        <div style="display:flex;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:100;align-items:center;justify-content:center;padding:20px;">
            <div style="background:#fff;border-radius:12px;padding:28px 32px;width:100%;max-width:440px;box-shadow:0 8px 32px rgba(0,0,0,0.15);">
                <h3 style="font-size:16px;font-weight:600;margin:0 0 12px;">Quitter le cercle</h3>
                <p style="font-size:14px;color:var(--fg-secondary);margin:0 0 24px;line-height:1.6;">
                    Voulez-vous vraiment quitter le cercle <strong>« {{ $circle->name }} »</strong> ?
                    Vous devrez soumettre une nouvelle demande pour le rejoindre à nouveau.
                </p>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" @click="open = false" class="fb-btn fb-btn-ghost fb-btn-sm">Annuler</button>
                    <form method="POST" action="{{ route('member.circles.leave', $circle) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="fb-btn fb-btn-primary fb-btn-sm" style="background:var(--brique-600);">
                            Confirmer et quitter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endpush
@endif

</x-layouts.member>
