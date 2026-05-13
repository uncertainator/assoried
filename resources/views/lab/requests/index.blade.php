<x-layouts.member title="Demandes reçues — Lab">

<div style="max-width:960px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:4px;">Lab</div>
            <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:0;letter-spacing:-.02em;">Demandes reçues</h1>
        </div>
    </div>

    @if($requests->isEmpty())
        <div style="text-align:center;padding:48px 24px;color:var(--fg-tertiary);background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);">
            Aucune demande reçue pour le moment.
        </div>
    @else
        <div style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--bg-subtle);border-bottom:1px solid var(--border-subtle);">
                        <th style="padding:12px 16px;text-align:left;font-size:var(--text-xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);">Cercle</th>
                        <th style="padding:12px 16px;text-align:left;font-size:var(--text-xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);">Service</th>
                        <th style="padding:12px 16px;text-align:left;font-size:var(--text-xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);">Message</th>
                        <th style="padding:12px 16px;text-align:left;font-size:var(--text-xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);">Date souhaitée</th>
                        <th style="padding:12px 16px;text-align:left;font-size:var(--text-xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);">Soumis le</th>
                        <th style="padding:12px 16px;text-align:left;font-size:var(--text-xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                        <tr style="border-bottom:1px solid var(--border-subtle);">
                            <td style="padding:14px 16px;vertical-align:top;">
                                <div style="font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);">{{ $req->circle->name }}</div>
                                <div style="font-size:var(--text-xs);color:var(--fg-tertiary);margin-top:2px;">{{ $req->user->name ?: $req->user->email }}</div>
                            </td>
                            <td style="padding:14px 16px;vertical-align:top;">
                                @if($req->labService)
                                    <span style="font-size:var(--text-sm);color:var(--fg-secondary);">{{ $req->labService->title }}</span>
                                @else
                                    <span style="font-size:var(--text-sm);color:var(--fg-tertiary);">—</span>
                                @endif
                            </td>
                            <td style="padding:14px 16px;vertical-align:top;max-width:280px;">
                                <span style="font-size:var(--text-sm);color:var(--fg-secondary);line-height:1.5;">{{ Str::limit($req->message, 120) }}</span>
                            </td>
                            <td style="padding:14px 16px;vertical-align:top;white-space:nowrap;">
                                <span style="font-size:var(--text-sm);color:var(--fg-secondary);">
                                    {{ $req->desired_date?->format('d/m/Y') ?? '—' }}
                                </span>
                            </td>
                            <td style="padding:14px 16px;vertical-align:top;white-space:nowrap;">
                                <span style="font-size:var(--text-sm);color:var(--fg-secondary);">{{ $req->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td style="padding:14px 16px;vertical-align:top;">
                                <form method="POST" action="{{ route('lab.requests.update-status', $req) }}" style="display:flex;align-items:center;gap:8px;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            style="padding:6px 8px;border:1px solid var(--border-default);border-radius:var(--radius-sm);font-size:var(--text-xs);background:var(--bg-base);color:var(--fg-primary);cursor:pointer;">
                                        @foreach($statuses as $s)
                                            <option value="{{ $s->value }}" {{ $req->status === $s ? 'selected' : '' }}>
                                                {{ $s->label() }}
                                            </option>
                                        @endforeach
                                    </select>
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
