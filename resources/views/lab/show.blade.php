<x-layouts.member :title="$service->title . ' — Lab'">

<div style="max-width:700px;">
    <div style="margin-bottom:24px;">
        <a href="{{ route('lab.services.index') }}" style="font-size:var(--text-sm);color:var(--fg-tertiary);text-decoration:underline;">← Retour au catalogue</a>
    </div>

    <div style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-xl);padding:32px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px;">
            <h1 style="font-family:var(--font-display);font-size:1.75rem;font-weight:600;color:var(--fg-primary);margin:0;letter-spacing:-.02em;">{{ $service->title }}</h1>
            <span class="fb-badge fb-badge-ocre" style="flex-shrink:0;">{{ $service->category }}</span>
        </div>

        <div style="font-size:var(--text-base);color:var(--fg-secondary);line-height:1.7;white-space:pre-wrap;">{{ $service->description }}</div>
    </div>

    <div style="display:flex;gap:8px;margin-top:20px;flex-wrap:wrap;">
        <a href="{{ route('lab.requests.create', ['service_id' => $service->id]) }}" class="fb-btn fb-btn-primary">Faire une demande</a>
        @can('update', $service)
            <a href="{{ route('lab.services.edit', $service) }}" class="fb-btn fb-btn-outline">Modifier</a>
            <form method="POST" action="{{ route('lab.services.destroy', $service) }}" style="margin:0;"
                  onsubmit="return confirm('Supprimer ce service définitivement ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="fb-btn fb-btn-ghost" style="color:var(--brique-600);">Supprimer</button>
            </form>
        @endcan
    </div>
</div>

</x-layouts.member>
