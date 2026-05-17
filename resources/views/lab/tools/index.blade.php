<x-layouts.member title="Boîte à outils — La Fabrique">

<div style="max-width:900px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:4px;">Lab</div>
            <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:0;letter-spacing:-.02em;">Boîte à outils</h1>
        </div>
        @can('create', App\Models\LabTool::class)
            <a href="{{ route('admin.lab.tools.create') }}" class="fb-btn fb-btn-outline">+ Nouvel outil</a>
        @endcan
    </div>

    @if ($tools->isEmpty())
        <div style="text-align:center;padding:48px 24px;color:var(--fg-tertiary);background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);">
            Aucun outil disponible pour le moment.
        </div>
    @else
        @foreach ($tools as $category => $items)
            <div style="margin-bottom:32px;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <span class="fb-badge fb-badge-ocre">{{ $category }}</span>
                    <span style="font-size:var(--text-sm);color:var(--fg-tertiary);">{{ $items->count() }} {{ Str::plural('outil', $items->count()) }}</span>
                </div>

                @foreach ($items as $tool)
                    <div style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:20px 24px;margin-bottom:10px;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:var(--text-base);font-weight:600;color:var(--fg-primary);margin-bottom:4px;">{{ $tool->title }}</div>
                                @if ($tool->description)
                                    <div style="font-size:var(--text-sm);color:var(--fg-secondary);line-height:1.5;">
                                        {{ Str::limit($tool->description, 150) }}
                                    </div>
                                @endif
                            </div>
                            <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
                                @if ($tool->downloads_count > 0)
                                    <span style="font-size:var(--text-xs);color:var(--fg-tertiary);">{{ $tool->downloads_count }} téléchargement{{ $tool->downloads_count > 1 ? 's' : '' }}</span>
                                @endif
                                <a href="{{ URL::temporarySignedRoute('lab.tools.download', now()->addHour(), $tool) }}"
                                   class="fb-btn fb-btn-primary"
                                   style="font-size:var(--text-sm);">
                                    Télécharger
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
</div>

</x-layouts.member>
