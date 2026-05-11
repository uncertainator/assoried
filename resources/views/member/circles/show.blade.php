<x-layouts.member :title="$circle->name.' — La Fabrique'">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">{{ $circle->name }}</h1>
        @if ($circle->description)
            <div class="ea-greeting-sub">{{ $circle->description }}</div>
        @endif
    </div>
    <a href="{{ route('member.circles.index') }}" class="fb-btn fb-btn-ghost fb-btn-sm">
        ← Tous les cercles
    </a>
</div>

@can('create', [App\Models\Post::class, $circle])
<div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:20px 24px;margin-bottom:28px;">
    <h2 style="font-size:15px;font-weight:600;margin:0 0 16px;">Nouvelle publication</h2>
    <form method="POST" action="{{ route('member.circles.posts.store', $circle) }}">
        @csrf
        <div style="margin-bottom:12px;">
            <textarea
                name="body"
                rows="4"
                maxlength="5000"
                placeholder="Rédigez votre publication…"
                style="width:100%;padding:10px 12px;border:1px solid var(--border-subtle);border-radius:6px;font-size:14px;resize:vertical;background:var(--surface-default);color:var(--fg-primary);"
                required
            >{{ old('body') }}</textarea>
            @error('body')
                <div style="color:var(--brique-500);font-size:13px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;">
                <input type="checkbox" name="push_to_general" value="1" {{ old('push_to_general') ? 'checked' : '' }}>
                Pousser au feed général
            </label>
            <button type="submit" class="fb-btn fb-btn-primary fb-btn-sm">Publier</button>
        </div>
    </form>
</div>
@endcan

<div>
    <h2 style="font-size:15px;font-weight:600;margin:0 0 16px;color:var(--fg-secondary);">
        Publications du cercle
    </h2>

    @if ($posts->isEmpty())
        <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">
            Aucune publication pour le moment.
        </p>
    @else
        <div style="display:flex;flex-direction:column;gap:16px;">
            @foreach ($posts as $post)
                <div style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:20px 24px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:var(--brique-100);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:var(--brique-700);">
                                {{ strtoupper(substr($post->author?->name ?: '?', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:14px;font-weight:500;">{{ $post->author?->name ?? 'Auteur supprimé' }}</div>
                                <div style="font-size:12px;color:var(--fg-tertiary);">{{ $post->created_at->translatedFormat('d F Y à H:i') }}</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            @if ($post->pushed_to_general)
                                <span class="fb-badge fb-badge-mousse">Poussé au feed général</span>
                            @endif
                            @if ($circle->isManagedBy(auth()->user()))
                                @unless ($post->pushed_to_general)
                                    <form method="POST" action="{{ route('member.posts.push', $post) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="fb-btn fb-btn-ghost fb-btn-sm">
                                            Pousser au feed général
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

        <div style="margin-top:24px;">
            {{ $posts->links() }}
        </div>
    @endif
</div>

</x-layouts.member>
