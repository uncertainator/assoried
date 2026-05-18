<x-layouts.admin title="Nouvelle consultation — Admin">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="fb-eyebrow">Administration</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:4px 0 0;letter-spacing:-.02em;">
            Nouvelle consultation
        </h1>
    </div>
    <a href="{{ route('admin.consultations.index') }}" class="fb-btn fb-btn-ghost fb-btn-sm">← Retour</a>
</div>

<div style="max-width:640px;">
    @include('admin.consultations._form', ['consultation' => null, 'action' => route('admin.consultations.store'), 'method' => 'POST'])
</div>

</x-layouts.admin>
