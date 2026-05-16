<x-layouts.member :title="'Journal — '.$circle->name.' — La Fabrique'">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Journal de bord</h1>
        <div class="ea-greeting-sub">{{ $circle->name }}</div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        @can('create', [App\Models\CircleJournalEntry::class, $circle])
            <a href="{{ route('member.circles.journal.create', $circle) }}" class="fb-btn fb-btn-primary fb-btn-sm">
                + Nouvelle entrée
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

@forelse ($entries as $entry)
    <div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:20px 24px;margin-bottom:16px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
            <div>
                <div style="font-size:13px;color:var(--fg-tertiary);margin-bottom:4px;">
                    {{ $entry->entry_date->translatedFormat('d F Y') }}
                    @if ($entry->author)
                        · {{ $entry->author->name }}
                    @endif
                </div>
                <div style="font-size:16px;font-weight:600;color:var(--fg-primary);">{{ $entry->title }}</div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;flex-wrap:wrap;">
                @can('update', $entry)
                    <a href="{{ route('member.circles.journal.edit', [$circle, $entry]) }}"
                       class="fb-btn fb-btn-ghost fb-btn-sm">Modifier</a>
                @endcan
                @can('delete', $entry)
                    <form method="POST" action="{{ route('member.circles.journal.destroy', [$circle, $entry]) }}"
                          onsubmit="return confirm('Supprimer cette entrée du journal ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm" style="color:var(--brique-500);">
                            Supprimer
                        </button>
                    </form>
                @endcan
            </div>
        </div>
        <div style="font-size:14px;line-height:1.7;color:var(--fg-primary);white-space:pre-wrap;">{{ $entry->content }}</div>
    </div>
@empty
    <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">Aucune entrée dans le journal pour le moment.</p>
@endforelse

@if ($entries->hasPages())
    <div style="margin-top:24px;">
        {{ $entries->links() }}
    </div>
@endif

</x-layouts.member>
