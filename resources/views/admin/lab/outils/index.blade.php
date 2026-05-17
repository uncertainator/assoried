<x-layouts.member title="Outils Lab — Admin">

<div style="max-width:960px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:4px;">Admin · Lab</div>
            <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:0;letter-spacing:-.02em;">Boîte à outils</h1>
        </div>
        <a href="{{ route('admin.lab.tools.create') }}" class="fb-btn fb-btn-primary">+ Nouvel outil</a>
    </div>

    @if (session('success'))
        <div style="background:var(--mousse-50);border:1px solid var(--mousse-200);border-radius:var(--radius-md);padding:12px 16px;margin-bottom:20px;color:var(--mousse-800);font-size:var(--text-sm);">
            {{ session('success') }}
        </div>
    @endif

    @if ($tools->isEmpty())
        <div style="text-align:center;padding:48px 24px;color:var(--fg-tertiary);background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);">
            Aucun outil pour le moment.
        </div>
    @else
        <div style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-subtle);">
                        <th style="text-align:left;padding:12px 16px;font-size:var(--text-sm);font-weight:600;color:var(--fg-secondary);">Titre</th>
                        <th style="text-align:left;padding:12px 16px;font-size:var(--text-sm);font-weight:600;color:var(--fg-secondary);">Catégorie</th>
                        <th style="text-align:center;padding:12px 16px;font-size:var(--text-sm);font-weight:600;color:var(--fg-secondary);">Téléchargements</th>
                        <th style="text-align:center;padding:12px 16px;font-size:var(--text-sm);font-weight:600;color:var(--fg-secondary);">Actif</th>
                        <th style="padding:12px 16px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tools as $tool)
                        <tr style="border-bottom:1px solid var(--border-subtle);">
                            <td style="padding:14px 16px;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);">{{ $tool->title }}</td>
                            <td style="padding:14px 16px;">
                                @if ($tool->category)
                                    <span class="fb-badge fb-badge-ocre">{{ $tool->category }}</span>
                                @else
                                    <span style="color:var(--fg-tertiary);font-size:var(--text-sm);">—</span>
                                @endif
                            </td>
                            <td style="padding:14px 16px;text-align:center;font-size:var(--text-sm);color:var(--fg-secondary);">{{ $tool->downloads_count }}</td>
                            <td style="padding:14px 16px;text-align:center;">
                                @if ($tool->active)
                                    <span class="fb-badge fb-badge-mousse">Actif</span>
                                @else
                                    <span class="fb-badge" style="background:var(--surface-muted);color:var(--fg-tertiary);">Inactif</span>
                                @endif
                            </td>
                            <td style="padding:14px 16px;text-align:right;white-space:nowrap;">
                                <a href="{{ route('admin.lab.tools.edit', $tool) }}" style="font-size:var(--text-sm);color:var(--fg-secondary);text-decoration:none;margin-right:12px;">Modifier</a>
                                <form action="{{ route('admin.lab.tools.destroy', $tool) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('Supprimer cet outil ? Le fichier sera définitivement supprimé.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="font-size:var(--text-sm);color:var(--brique-600);background:none;border:none;cursor:pointer;padding:0;">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

</x-layouts.member>
