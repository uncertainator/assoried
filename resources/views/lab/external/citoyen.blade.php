<x-layouts.app title="Programme citoyen — Lab La Fabrique">

<div style="max-width:720px;margin:0 auto;padding:48px 24px;">

    <div style="margin-bottom:40px;">
        <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:6px;">Lab La Fabrique · Programme citoyen</div>
        <h1 style="font-family:var(--font-display);font-size:2.25rem;font-weight:700;color:var(--fg-primary);margin:0 0 16px;letter-spacing:-.02em;">Je monte un projet,<br>j'entreprends</h1>
        <p style="font-size:var(--text-base);color:var(--fg-secondary);line-height:1.7;max-width:60ch;">
            Vous avez une idée, un projet de territoire ou une envie d'entreprendre ?
            Le Lab vous accompagne : ateliers de facilitation, mise en réseau, accès à des ressources et à une communauté engagée.
            Décrivez votre projet ci-dessous et un référent Lab vous recontactera.
        </p>
    </div>

    <div id="form-citoyen">
        @include('lab.external._form-citoyen')
    </div>
</div>

</x-layouts.app>
