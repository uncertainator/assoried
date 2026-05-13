<x-layouts.app title="Demande reçue — Lab La Fabrique">

<div style="max-width:600px;margin:80px auto;padding:0 24px;text-align:center;">
    <div style="width:64px;height:64px;background:var(--bg-surface);border:2px solid var(--border-subtle);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--color-mousse-600);">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    </div>
    <h1 style="font-family:var(--font-display);font-size:1.75rem;font-weight:700;color:var(--fg-primary);margin:0 0 12px;letter-spacing:-.02em;">Demande bien reçue !</h1>
    <p style="font-size:var(--text-base);color:var(--fg-secondary);line-height:1.7;margin:0 0 8px;">
        Merci pour votre message. L'équipe du Lab La Fabrique va étudier votre demande et vous recontactera dans les meilleurs délais.
    </p>
    <p style="font-size:var(--text-sm);color:var(--fg-tertiary);line-height:1.6;margin:0 0 32px;">
        Un email de confirmation vous a été envoyé. Pensez à vérifier vos courriers indésirables.
    </p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ route('home') }}" class="fb-btn fb-btn-primary">Retour à l'accueil</a>
        <a href="{{ route('public.agenda') }}" class="fb-btn fb-btn-outline">Voir l'agenda public</a>
    </div>
</div>

</x-layouts.app>
