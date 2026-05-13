<x-layouts.member title="Demandes externes — Lab">

<div style="max-width:1080px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:4px;">Lab</div>
            <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:0;letter-spacing:-.02em;">Demandes externes</h1>
        </div>
        {{-- Filtre type --}}
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('lab.external.index') }}"
               class="fb-btn fb-btn-sm {{ !request('type') ? 'fb-btn-primary' : 'fb-btn-outline' }}">
                Toutes
            </a>
            <a href="{{ route('lab.external.index', ['type' => 'citoyen']) }}"
               class="fb-btn fb-btn-sm {{ request('type') === 'citoyen' ? 'fb-btn-primary' : 'fb-btn-outline' }}">
                Citoyens
            </a>
            <a href="{{ route('lab.external.index', ['type' => 'entreprise']) }}"
               class="fb-btn fb-btn-sm {{ request('type') === 'entreprise' ? 'fb-btn-primary' : 'fb-btn-outline' }}">
                Entreprises
            </a>
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
                        <th style="padding:12px 16px;text-align:left;font-size:var(--text-xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);">Type</th>
                        <th style="padding:12px 16px;text-align:left;font-size:var(--text-xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);">Identité</th>
                        <th style="padding:12px 16px;text-align:left;font-size:var(--text-xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);">Email</th>
                        <th style="padding:12px 16px;text-align:left;font-size:var(--text-xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);">Message</th>
                        <th style="padding:12px 16px;text-align:left;font-size:var(--text-xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);">Reçue le</th>
                        <th style="padding:12px 16px;text-align:left;font-size:var(--text-xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-tertiary);">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                        <tr style="border-bottom:1px solid var(--border-subtle);">
                            <td style="padding:14px 16px;vertical-align:top;">
                                <span class="fb-badge {{ $req->type === 'citoyen' ? 'fb-badge-ocre' : 'fb-badge-brique' }}">
                                    {{ $req->type === 'citoyen' ? 'Citoyen' : 'Entreprise' }}
                                </span>
                                @if($req->besoin_type)
                                    <div style="font-size:var(--text-xs);color:var(--fg-tertiary);margin-top:4px;">{{ ucfirst($req->besoin_type) }}</div>
                                @endif
                            </td>
                            <td style="padding:14px 16px;vertical-align:top;">
                                <div style="font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);">{{ $req->nom_contact }}</div>
                                @if($req->raison_sociale)
                                    <div style="font-size:var(--text-xs);color:var(--fg-tertiary);margin-top:2px;">{{ $req->raison_sociale }}</div>
                                @endif
                                @if($req->territoire)
                                    <div style="font-size:var(--text-xs);color:var(--fg-tertiary);margin-top:2px;">{{ $req->territoire }}</div>
                                @endif
                                @if($req->telephone)
                                    <div style="font-size:var(--text-xs);color:var(--fg-tertiary);margin-top:2px;">{{ $req->telephone }}</div>
                                @endif
                            </td>
                            <td style="padding:14px 16px;vertical-align:top;">
                                <a href="mailto:{{ $req->email }}" style="font-size:var(--text-sm);color:var(--fg-secondary);">{{ $req->email }}</a>
                            </td>
                            <td style="padding:14px 16px;vertical-align:top;max-width:260px;">
                                <span style="font-size:var(--text-sm);color:var(--fg-secondary);line-height:1.5;">{{ Str::limit($req->message, 120) }}</span>
                            </td>
                            <td style="padding:14px 16px;vertical-align:top;white-space:nowrap;">
                                <span style="font-size:var(--text-sm);color:var(--fg-secondary);">{{ $req->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td style="padding:14px 16px;vertical-align:top;">
                                <form method="POST" action="{{ route('lab.external.update-status', $req) }}" style="display:flex;align-items:center;gap:8px;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="statut" onchange="this.form.submit()"
                                            style="padding:6px 8px;border:1px solid var(--border-default);border-radius:var(--radius-sm);font-size:var(--text-xs);background:var(--bg-base);color:var(--fg-primary);cursor:pointer;">
                                        @foreach($statuses as $s)
                                            <option value="{{ $s->value }}" {{ $req->statut === $s ? 'selected' : '' }}>
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
