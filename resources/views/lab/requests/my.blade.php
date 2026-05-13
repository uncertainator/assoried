<x-layouts.member title="Mes demandes Lab — La Fabrique">

<div style="max-width:800px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:4px;">Lab</div>
            <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:0;letter-spacing:-.02em;">Mes demandes de soutien</h1>
        </div>
        <a href="{{ route('lab.requests.create') }}" class="fb-btn fb-btn-primary">+ Nouvelle demande</a>
    </div>

    @if($requests->isEmpty())
        <div style="text-align:center;padding:48px 24px;color:var(--fg-tertiary);background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);">
            <p style="margin:0 0 16px;">Vous n'avez pas encore soumis de demande.</p>
            <a href="{{ route('lab.requests.create') }}" class="fb-btn fb-btn-primary">Faire une demande au Lab</a>
        </div>
    @else
        @foreach($requests as $req)
            <div style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:20px 24px;margin-bottom:12px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:12px;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:var(--text-base);font-weight:600;color:var(--fg-primary);margin-bottom:2px;">{{ $req->circle->name }}</div>
                        @if($req->labService)
                            <div style="font-size:var(--text-sm);color:var(--fg-secondary);">{{ $req->labService->title }}</div>
                        @endif
                    </div>
                    <span class="fb-badge {{ $req->status->badgeClass() }}" style="white-space:nowrap;flex-shrink:0;">{{ $req->status->label() }}</span>
                </div>
                <p style="margin:0 0 10px;font-size:var(--text-sm);color:var(--fg-secondary);line-height:1.6;">{{ Str::limit($req->message, 200) }}</p>
                <div style="display:flex;gap:16px;font-size:var(--text-xs);color:var(--fg-tertiary);">
                    <span>Soumis le {{ $req->created_at->format('d/m/Y') }}</span>
                    @if($req->desired_date)
                        <span>· Date souhaitée : {{ $req->desired_date->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>

</x-layouts.member>
