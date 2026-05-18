<x-layouts.admin :title="'Modifier — '.$consultation->titre">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Administration</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Modifier la consultation
        </h1>
    </div>
    <a href="{{ route('admin.consultations.show', $consultation) }}" class="fb-btn fb-btn-ghost fb-btn-sm">← Retour</a>
</div>

@if ($consultation->estCloturee())
    <div style="background:var(--brique-100);border:1px solid var(--brique-300);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--brique-700);">
        Cette consultation est clôturée et ne peut plus être modifiée.
    </div>
@else
    <div style="max-width:640px;">
        @include('admin.consultations._form', [
            'consultation' => $consultation,
            'action' => route('admin.consultations.update', $consultation),
            'method' => 'PUT',
        ])
    </div>
@endif

</x-layouts.admin>
