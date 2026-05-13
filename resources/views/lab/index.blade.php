<x-layouts.member title="Catalogue Lab — La Fabrique">

<div style="max-width:900px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:4px;">Lab</div>
            <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:0;letter-spacing:-.02em;">Catalogue de services</h1>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('lab.requests.create') }}" class="fb-btn fb-btn-primary">Faire une demande</a>
            @can('create', App\Models\LabService::class)
                <a href="{{ route('lab.services.create') }}" class="fb-btn fb-btn-outline">+ Nouveau service</a>
            @endcan
        </div>
    </div>

    @forelse ($services as $service)
        <a href="{{ route('lab.services.show', $service) }}"
           style="display:block;text-decoration:none;background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:20px 24px;margin-bottom:12px;transition:box-shadow .15s;"
           onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow='none'">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:var(--text-base);font-weight:600;color:var(--fg-primary);margin-bottom:4px;">{{ $service->title }}</div>
                    <div style="font-size:var(--text-sm);color:var(--fg-secondary);line-height:1.5;">
                        {{ Str::limit($service->description, 150) }}
                    </div>
                </div>
                <span class="fb-badge fb-badge-ocre" style="white-space:nowrap;flex-shrink:0;">{{ $service->category }}</span>
            </div>
        </a>
    @empty
        <div style="text-align:center;padding:48px 24px;color:var(--fg-tertiary);background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);">
            Aucun service dans le catalogue pour le moment.
        </div>
    @endforelse
</div>

</x-layouts.member>
