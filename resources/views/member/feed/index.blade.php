<x-layouts.member title="Feed général — La Fabrique">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Feed général</h1>
        <div class="ea-greeting-sub">Publications choisies par les référents de tous les cercles.</div>
    </div>
</div>

<div>
    @if ($posts->isEmpty())
        <p style="font-size:14px;color:var(--fg-tertiary);font-style:italic;">
            Aucune publication dans le feed général pour le moment.
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
                                <div style="font-size:12px;color:var(--fg-tertiary);">{{ $post->pushed_at->translatedFormat('d F Y à H:i') }}</div>
                            </div>
                        </div>
                        <a href="{{ route('member.circles.show', $post->circle) }}"
                           style="text-decoration:none;">
                            <span class="fb-badge fb-badge-ocre">{{ $post->circle->name }}</span>
                        </a>
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
