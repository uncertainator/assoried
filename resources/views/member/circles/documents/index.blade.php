<x-layouts.member :title="'Bibliothèque — '.$circle->name.' — La Fabrique'">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Bibliothèque</h1>
        <div class="ea-greeting-sub">{{ $circle->name }}</div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        @can('create', [App\Models\CircleDocument::class, $circle])
            <a href="{{ route('referent.circle.documents.create', $circle) }}" class="fb-btn fb-btn-primary fb-btn-sm">
                + Ajouter un document
            </a>
        @endcan
        <a href="{{ route('member.circles.show', $circle) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
            ← Retour au cercle
        </a>
    </div>
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

{{-- Filtre par tag --}}
@if ($allTags->isNotEmpty())
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
        <span style="font-size:13px;color:var(--fg-tertiary);">Filtrer :</span>
        <a href="{{ route('member.circles.documents.index', $circle) }}"
           style="font-size:13px;padding:4px 10px;border-radius:20px;text-decoration:none;
                  background:{{ is_null($tag) ? 'var(--brique-500)' : 'var(--surface-raised)' }};
                  color:{{ is_null($tag) ? '#fff' : 'var(--fg-secondary)' }};
                  border:1px solid {{ is_null($tag) ? 'var(--brique-500)' : 'var(--border-subtle)' }};">
            Tous
        </a>
        @foreach ($allTags as $t)
            <a href="{{ route('member.circles.documents.index', [$circle, 'tag' => $t]) }}"
               style="font-size:13px;padding:4px 10px;border-radius:20px;text-decoration:none;
                      background:{{ $tag === $t ? 'var(--brique-500)' : 'var(--surface-raised)' }};
                      color:{{ $tag === $t ? '#fff' : 'var(--fg-secondary)' }};
                      border:1px solid {{ $tag === $t ? 'var(--brique-500)' : 'var(--border-subtle)' }};">
                {{ $t }}
            </a>
        @endforeach
    </div>
@endif

{{-- Liste des documents --}}
@forelse ($documents as $document)
    <div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:20px 24px;margin-bottom:16px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;color:var(--fg-tertiary);margin-bottom:6px;">
                    {{ $document->document_date->translatedFormat('d F Y') }}
                    &nbsp;·&nbsp;
                    @if ($document->isPdf())
                        <span style="color:var(--brique-500);">PDF</span>
                    @else
                        <span style="color:var(--mousse-600);">Lien</span>
                    @endif
                </div>

                <div style="font-size:15px;font-weight:600;color:var(--fg-primary);margin-bottom:8px;">
                    {{ $document->title }}
                </div>

                @if ($document->description)
                    <div style="font-size:13px;color:var(--fg-secondary);margin-bottom:8px;line-height:1.5;">
                        {{ $document->description }}
                    </div>
                @endif

                @if (!empty($document->tags))
                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:10px;">
                        @foreach ($document->tags as $t)
                            <span style="font-size:12px;padding:2px 8px;border-radius:12px;background:var(--ocre-100);color:var(--ocre-700);">
                                {{ $t }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if ($document->isPdf())
                    <a href="{{ $document->getStorageUrl() }}"
                       target="_blank"
                       rel="noopener"
                       class="fb-btn fb-btn-ghost fb-btn-sm">
                        ↓ Télécharger ({{ $document->original_filename }})
                    </a>
                @else
                    <a href="{{ $document->url }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="fb-btn fb-btn-ghost fb-btn-sm">
                        ↗ Ouvrir le lien
                    </a>
                @endif
            </div>

            @can('delete', $document)
                <form method="POST"
                      action="{{ route('referent.circle.documents.destroy', [$circle, $document]) }}"
                      onsubmit="return confirm('Supprimer ce document ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="fb-btn fb-btn-ghost fb-btn-sm"
                            style="color:var(--brique-500);flex-shrink:0;">
                        Supprimer
                    </button>
                </form>
            @endcan
        </div>
    </div>
@empty
    <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">
        Aucun document dans la bibliothèque pour le moment.
    </p>
@endforelse

</x-layouts.member>
