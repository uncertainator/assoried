<x-layouts.app title="Programme entreprise — Lab La Fabrique">

<div style="max-width:720px;margin:0 auto;padding:48px 24px;">

    <div style="margin-bottom:40px;">
        <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:6px;">Lab La Fabrique · Programme entreprise</div>
        <h1 style="font-family:var(--font-display);font-size:2.25rem;font-weight:700;color:var(--fg-primary);margin:0 0 16px;letter-spacing:-.02em;">Mon entreprise<br>fait appel au Lab</h1>
        <p style="font-size:var(--text-base);color:var(--fg-secondary);line-height:1.7;max-width:60ch;">
            Le Lab accompagne les entreprises du territoire qui souhaitent innover, faciliter leur organisation interne ou
            explorer de nouvelles approches collaboratives. Déposez votre demande et un référent Lab prendra contact avec vous.
        </p>
    </div>

    <div id="form-entreprise">
        @include('lab.external._form-entreprise')
    </div>
</div>

</x-layouts.app>
