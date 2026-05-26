<x-layouts.app title="Lab La Fabrique — Déposez votre demande">

<div style="max-width:760px;margin:0 auto;padding:48px 24px;">

    <div style="margin-bottom:56px;">
        <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:6px;">Lab La Fabrique</div>
        <h1 style="font-family:var(--font-display);font-size:2.5rem;font-weight:700;color:var(--fg-primary);margin:0 0 16px;letter-spacing:-.02em;">Déposez votre demande</h1>
        <p style="font-size:var(--text-base);color:var(--fg-secondary);line-height:1.7;max-width:60ch;">
            Le Lab accompagne citoyens, associations et entreprises du territoire. Choisissez le formulaire qui correspond à votre situation.
        </p>
    </div>

    <section id="form-citoyen" style="margin-bottom:72px;">
        <div style="margin-bottom:28px;">
            <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:6px;">Programme citoyen</div>
            <h2 style="font-family:var(--font-display);font-size:1.75rem;font-weight:700;color:var(--fg-primary);margin:0 0 12px;letter-spacing:-.02em;">Je monte un projet,<br>j'entreprends</h2>
            <p style="font-size:var(--text-base);color:var(--fg-secondary);line-height:1.7;max-width:60ch;">
                Vous avez une idée, un projet de territoire ou une envie d'entreprendre ?
                Le Lab vous accompagne : ateliers de facilitation, mise en réseau, accès à des ressources et à une communauté engagée.
                C'est gratuit.
            </p>
        </div>
        @include('lab.external._form-citoyen')
    </section>

    <hr style="border:none;border-top:1px solid var(--border-subtle);margin-bottom:72px;">

    <section id="form-entreprise">
        <div style="margin-bottom:28px;">
            <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:6px;">Programme entreprise</div>
            <h2 style="font-family:var(--font-display);font-size:1.75rem;font-weight:700;color:var(--fg-primary);margin:0 0 12px;letter-spacing:-.02em;">Mon entreprise<br>fait appel au Lab</h2>
            <p style="font-size:var(--text-base);color:var(--fg-secondary);line-height:1.7;max-width:60ch;">
                Le Lab accompagne les entreprises du territoire qui souhaitent innover, faciliter leur organisation ou
                explorer de nouvelles approches collaboratives. Déposez votre demande et un référent Lab vous recontactera.
            </p>
        </div>
        @include('lab.external._form-entreprise')
    </section>

</div>

</x-layouts.app>
