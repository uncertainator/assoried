<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'mentions-legales',
                'title' => 'Mentions légales',
                'content' => '<h2>Éditeur</h2><p>La Fabrique — association citoyenne en cours de constitution. Alsace, France. Contact : bonjour@lafabrique.fr</p><h2>Hébergement</h2><p>Ce site est hébergé par Planethoster. 4416 Louis-B.-Mayer, Laval, Québec H7P 0G1, Canada.</p>',
            ],
            [
                'slug' => 'confidentialite',
                'title' => 'Politique de confidentialité',
                'content' => <<<'HTML'
<p><em>Dernière mise à jour : 12 juin 2026</em></p>
<p>La présente politique décrit comment l'association Hop'Initiatives (ci-après «&nbsp;l'Association&nbsp;») collecte, utilise et protège les données personnelles des utilisateurs de son site web et de son application de gestion des membres (ci-après «&nbsp;la Plateforme&nbsp;»).</p>
<p>Elle est conforme au Règlement général sur la protection des données (RGPD, règlement UE 2016/679) et à la loi française «&nbsp;Informatique et Libertés&nbsp;» du 6 janvier 1978 modifiée.</p>
<h2>1. Responsable du traitement</h2>
<p>Le responsable du traitement est&nbsp;:</p>
<p><strong>Hop'Initiatives</strong><br>Association régie par le droit local d'Alsace-Moselle (Code civil local)<br>Siège&nbsp;: 11 place de la République, 67230 Benfeld<br>Contact en matière de données personnelles&nbsp;: <a href="mailto:rgpd@hopinitiatives.org">rgpd@hopinitiatives.org</a></p>
<p>Pour toute question relative à vos données ou à l'exercice de vos droits, vous pouvez écrire à l'adresse de contact ci-dessus.</p>
<h2>2. Données collectées</h2>
<p>L'Association collecte uniquement les données nécessaires à son fonctionnement et à la gestion de ses membres.</p>
<h3>2.1 Données d'identification et de compte</h3>
<ul><li>Nom et prénom</li><li>Adresse email</li><li>Mot de passe (stocké sous forme chiffrée et irréversible — l'Association n'y a jamais accès en clair)</li><li>Rôle au sein de la Plateforme (adhérent, référent, administrateur)</li></ul>
<h3>2.2 Données liées à la vie associative</h3>
<ul><li>Cercle(s) thématique(s) d'appartenance</li><li>Participation aux votes, événements et projets</li><li>Contributions aux projets citoyens soumis via la Plateforme</li></ul>
<h3>2.3 Données techniques</h3>
<ul><li>Données de connexion strictement nécessaires au fonctionnement du service (authentification par lien magique ou mot de passe, gestion de session)</li><li>Adresse IP, journaux techniques de sécurité (logs serveur), conservés à des fins de sécurité et de prévention des accès frauduleux</li></ul>
<p>L'Association <strong>n'utilise aucun outil de mesure d'audience ni de traçage publicitaire</strong> (pas d'analytics, pas de cookies tiers à des fins marketing).</p>
<h3>2.4 Données susceptibles de révéler une opinion politique</h3>
<p>L'adhésion à l'Association et la participation à ses activités peuvent, par leur nature, révéler une opinion ou une orientation politique. Ces informations relèvent des <strong>catégories particulières de données</strong> au sens de l'article 9 du RGPD.</p>
<p>À ce titre&nbsp;:</p>
<ul><li>elles ne sont collectées qu'avec votre <strong>consentement explicite</strong>, donné au moment de l'adhésion&nbsp;;</li><li>elles ne sont accessibles qu'aux personnes habilitées au sein de l'Association (administrateurs et, dans la limite de leur cercle, référents)&nbsp;;</li><li>elles ne font l'objet d'aucune diffusion publique, cession ou communication à des tiers.</li></ul>
<h2>3. Finalités et bases légales du traitement</h2>
<ul><li><strong>Création et gestion du compte membre</strong> — Exécution du contrat d'adhésion (art. 6.1.b)</li><li><strong>Gestion de la vie associative (cercles, votes, événements, projets)</strong> — Exécution du contrat d'adhésion (art. 6.1.b)</li><li><strong>Authentification et sécurité de la Plateforme</strong> — Intérêt légitime de l'Association (art. 6.1.f)</li><li><strong>Traitement des données révélant une opinion politique</strong> — Consentement explicite de la personne (art. 9.2.a)</li><li><strong>Envoi de communications liées à la vie de l'Association</strong> — Exécution du contrat d'adhésion / consentement selon le cas</li></ul>
<p>Vous pouvez retirer votre consentement à tout moment, sans que cela remette en cause la licéité des traitements antérieurs.</p>
<h2>4. Destinataires des données</h2>
<p>Les données ne sont accessibles qu'aux personnes habilitées au sein de l'Association, dans la stricte limite de leurs fonctions&nbsp;:</p>
<ul><li>les <strong>administrateurs</strong>, pour la gestion globale de la Plateforme&nbsp;;</li><li>les <strong>référents</strong>, pour la seule gestion de leur cercle.</li></ul>
<p>L'Association <strong>ne vend, ne loue et ne cède aucune donnée</strong> à des tiers à des fins commerciales.</p>
<h2>5. Sous-traitant et hébergement</h2>
<p>Les données sont hébergées et traitées techniquement par notre prestataire d'hébergement, qui agit en qualité de <strong>sous-traitant</strong> au sens de l'article 28 du RGPD&nbsp;:</p>
<p><strong>PlanetHoster</strong> — hébergement mutualisé sur infrastructure située dans l'<strong>Union européenne</strong>.</p>
<p>Ce prestataire est lié à l'Association par un accord de sous-traitance encadrant le traitement des données, et n'est autorisé à les traiter que sur instruction de l'Association, à des fins exclusivement techniques (stockage, sauvegarde, acheminement des emails de service).</p>
<p>Aucune donnée n'est transférée en dehors de l'Union européenne.</p>
<h2>6. Durée de conservation</h2>
<ul><li><strong>Données du compte membre</strong> — Pendant toute la durée de l'adhésion</li><li><strong>Données après départ / radiation</strong> — Supprimées ou anonymisées dans un délai raisonnable suivant la perte de la qualité de membre, sauf obligation légale contraire</li><li><strong>Journaux techniques de sécurité (logs)</strong> — Durée limitée nécessaire à la sécurité, n'excédant pas 12 mois</li></ul>
<p>À l'expiration de ces durées, les données sont supprimées ou anonymisées de manière irréversible.</p>
<h2>7. Sécurité des données</h2>
<p>L'Association met en œuvre des mesures techniques et organisationnelles adaptées pour protéger les données contre tout accès, altération, divulgation ou destruction non autorisés, notamment&nbsp;:</p>
<ul><li>chiffrement irréversible des mots de passe&nbsp;;</li><li>authentification par lien à usage unique (lien magique) ou mot de passe&nbsp;;</li><li>gestion stricte des droits d'accès selon les rôles&nbsp;;</li><li>hébergement sur infrastructure sécurisée au sein de l'Union européenne.</li></ul>
<p>Compte tenu de la nature potentiellement sensible des données (opinions politiques), l'Association porte une attention particulière à la confidentialité et à la limitation des accès.</p>
<h2>8. Vos droits</h2>
<p>Conformément au RGPD, vous disposez des droits suivants&nbsp;:</p>
<ul><li><strong>droit d'accès</strong>&nbsp;: obtenir la confirmation que vos données sont traitées et en obtenir une copie&nbsp;;</li><li><strong>droit de rectification</strong>&nbsp;: corriger des données inexactes ou incomplètes&nbsp;;</li><li><strong>droit à l'effacement</strong>&nbsp;: demander la suppression de vos données&nbsp;;</li><li><strong>droit à la limitation</strong> du traitement&nbsp;;</li><li><strong>droit d'opposition</strong> au traitement fondé sur l'intérêt légitime&nbsp;;</li><li><strong>droit à la portabilité</strong> de vos données&nbsp;;</li><li><strong>droit de retirer votre consentement</strong> à tout moment&nbsp;;</li><li><strong>droit de définir des directives</strong> relatives au sort de vos données après votre décès.</li></ul>
<p>Vous pouvez exercer ces droits en écrivant à&nbsp;: <a href="mailto:rgpd@hopinitiatives.org">rgpd@hopinitiatives.org</a>. Une preuve d'identité pourra vous être demandée.</p>
<p>L'Association s'engage à répondre dans un délai d'un mois.</p>
<p>Si vous estimez que vos droits ne sont pas respectés, vous pouvez introduire une réclamation auprès de la <strong>Commission nationale de l'informatique et des libertés (CNIL)</strong> — <a href="https://www.cnil.fr">www.cnil.fr</a>.</p>
<h2>9. Cookies</h2>
<p>La Plateforme utilise uniquement des <strong>cookies strictement nécessaires</strong> à son fonctionnement (gestion de la session d'authentification). Ces cookies ne requièrent pas de consentement préalable et ne servent ni à la publicité, ni au suivi de votre navigation.</p>
<p>Aucun cookie de mesure d'audience ou de traçage tiers n'est déposé.</p>
<h2>10. Modification de la présente politique</h2>
<p>L'Association peut être amenée à modifier la présente politique pour l'adapter à l'évolution du service ou de la réglementation. La date de dernière mise à jour figure en tête de document. En cas de modification substantielle, les membres en seront informés.</p>
HTML,
            ],
            [
                'slug' => 'a-propos',
                'title' => 'Qui sommes-nous',
                'content' => "<p>La Fabrique est une association citoyenne implantée en Alsace. Elle réunit des habitants engagés autour de cercles thématiques : mobilité, alimentation, numérique, et bien d'autres.</p><p>Notre mission est de favoriser les initiatives locales et de renforcer les liens entre citoyens.</p>",
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
