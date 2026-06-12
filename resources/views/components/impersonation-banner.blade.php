{{-- Persistent return banner shown while a superadmin endorses a lower role.
     Driven purely by the session, NOT the role system — so a superadmin viewing
     as adherent always keeps a way back. --}}
@php($impersonatedRole = session('impersonate_role'))
@if ($impersonatedRole)
    @php($roleLabel = \App\Enums\UserRole::from($impersonatedRole)->label())
    <div style="position:sticky;top:0;z-index:1000;display:flex;align-items:center;justify-content:center;gap:14px;padding:10px 16px;background:#c85226;color:#fff;font-size:14px;font-weight:600;box-shadow:0 1px 4px rgba(0,0,0,.2);">
        <span>👁️ Vous visualisez en tant que {{ $roleLabel }}</span>
        <form method="POST" action="{{ route('impersonate.stop') }}" style="margin:0;">
            @csrf
            <button type="submit" style="background:#fff;color:#c85226;border:none;border-radius:6px;padding:5px 12px;font-size:13px;font-weight:700;cursor:pointer;">
                Revenir à superadmin
            </button>
        </form>
    </div>
@endif
