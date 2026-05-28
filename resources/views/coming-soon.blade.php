<x-layouts.app :title="($title ?? 'La Fabrique') . ' — La Fabrique'">

<section style="max-width:640px;margin:96px auto;padding:0 24px;text-align:center;">
    <img src="{{ asset('images/logo-mark.svg') }}" alt="" width="64" height="64" style="margin-bottom:24px;opacity:.4;">
    <div class="fb-eyebrow" style="text-align:center;">À venir</div>
    <h1 style="font-family:var(--font-display);font-size:2.5rem;font-weight:600;color:var(--fg-primary);margin:8px 0 16px;letter-spacing:-.02em;text-wrap:balance;">
        {{ $soon ?? $title ?? 'Cette section' }} arrive bientôt.
    </h1>
    <p class="fb-lead" style="margin-bottom:40px;">
        On travaille dessus. En attendant, rejoignez La Fabrique pour être informé en avant-première.
    </p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ route('inscription') }}" class="fb-btn fb-btn-primary fb-btn-lg">Rester informé →</a>
        <a href="{{ route('home') }}" class="fb-btn fb-btn-outline fb-btn-lg">← Retour à l'accueil</a>
    </div>
</section>

</x-layouts.app>
