<?php

// This seeder populates the database with a comprehensive set of test data covering all main entities and edge cases.

namespace Database\Seeders;

use App\Enums\CircleActionStatus;
use App\Enums\ConsultationMode;
use App\Enums\LabRequestStatus;
use App\Enums\ScrutinMajorityType;
use App\Enums\ScrutinQuorumType;
use App\Models\Circle;
use App\Models\CircleAction;
use App\Models\CircleDocument;
use App\Models\CircleJournalEntry;
use App\Models\CircleMembership;
use App\Models\Consultation;
use App\Models\ConsultationReponse;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\LabExternalRequest;
use App\Models\LabInternalRequest;
use App\Models\LabService;
use App\Models\LabTool;
use App\Models\Meeting;
use App\Models\MeetingAgendaItem;
use App\Models\MeetingReport;
use App\Models\Poll;
use App\Models\PollVote;
use App\Models\Post;
use App\Models\Scrutin;
use App\Models\ScrutinOption;
use App\Models\ScrutinVote;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ══════════════════════════════════════════════════════════════
        // 1. USERS
        // ══════════════════════════════════════════════════════════════
        $admin = User::where('email', 'admin@lafabrique.fr')->firstOrFail();

        $referent1 = User::factory()->referent()->create([
            'name' => 'Marie Dupont',
            'email' => 'marie.dupont@test.fr',
        ]);
        $referent2 = User::factory()->referent()->create([
            'name' => 'Jean Martin',
            'email' => 'jean.martin@test.fr',
        ]);
        $referent3 = User::factory()->referent()->create([
            'name' => 'Sophie Blanc',
            'email' => 'sophie.blanc@test.fr',
        ]);

        // Adhérents avec mot de passe (password = "password")
        $adherents = User::factory()->adherent()->count(12)->create();

        // Adhérent magic-link uniquement (pas de mdp)
        User::factory()->magicLinkOnly()->create([
            'name' => 'Alice Magic',
            'email' => 'alice.magic@test.fr',
        ]);
        // Adhérent ayant refusé la config mot de passe
        User::factory()->withDismissedSetup()->create([
            'name' => 'Bob Sans Mdp',
            'email' => 'bob.sansMdp@test.fr',
        ]);

        // Comptes en attente de validation d'adhésion par le bureau.
        // Statut "pending" → ne peuvent pas se connecter tant que non validés.
        $pendingAvecNom = User::factory()->pending()->create([
            'name' => 'Camille Nouvelle',
            'email' => 'camille.nouvelle@test.fr',
        ]);
        $pendingSansNom = User::factory()->pending()->create([
            'name' => '', // inscription par mot de passe : nom encore vide
            'email' => 'pierre.attente@test.fr',
        ]);
        // En attente, inscrit par magic link (pas de mot de passe)
        User::factory()->pending()->magicLinkOnly()->create([
            'name' => 'Nadia EnAttente',
            'email' => 'nadia.attente@test.fr',
        ]);
        // En attente + email non vérifié (lien de confirmation pas encore cliqué)
        User::factory()->pending()->unverified()->create([
            'name' => 'Karim Inscription',
            'email' => 'karim.inscription@test.fr',
        ]);
        // Lot de demandes en attente pour tester la liste de validation
        User::factory()->pending()->count(3)->create();

        // ══════════════════════════════════════════════════════════════
        // 2. CIRCLES — déjà seedés, on assigne les référents
        // ══════════════════════════════════════════════════════════════
        $mobilite = Circle::where('slug', 'mobilite')->first();
        $finance = Circle::where('slug', 'finance')->first();
        $pilotage = Circle::where('slug', 'pilotage')->first();
        $lab = Circle::where('slug', 'lab')->first();
        $comms = Circle::where('slug', 'communication')->first();
        $intergen = Circle::where('slug', 'intergenerationnel')->first();

        $mobilite->update(['referent_id' => $referent1->id]);
        $finance->update(['referent_id' => $referent2->id]);
        $pilotage->update(['referent_id' => $referent3->id]);
        $lab->update(['referent_id' => $referent1->id]);

        // Cercles d'intérêt choisis à l'inscription par les comptes en attente
        $pendingAvecNom->circles()->attach($mobilite->id, ['joined_at' => now()]);
        $pendingAvecNom->circles()->attach($lab->id, ['joined_at' => now()]);
        $pendingSansNom->circles()->attach($finance->id, ['joined_at' => now()]);

        // ══════════════════════════════════════════════════════════════
        // 3. MEMBERSHIPS — tous les cas
        // ══════════════════════════════════════════════════════════════

        // Référents approuvés dans leur cercle principal
        foreach ([
            [$referent1, $mobilite],
            [$referent2, $finance],
            [$referent3, $pilotage],
        ] as [$ref, $circle]) {
            CircleMembership::factory()->approved()->create([
                'user_id' => $ref->id,
                'circle_id' => $circle->id,
            ]);
        }

        // Adhérents dans Mobilité
        foreach ($adherents->take(4) as $user) {
            CircleMembership::factory()->approved()->create([
                'user_id' => $user->id, 'circle_id' => $mobilite->id,
            ]);
        }
        foreach ($adherents->slice(4, 2) as $user) {
            CircleMembership::factory()->pending()->create([
                'user_id' => $user->id, 'circle_id' => $mobilite->id,
            ]);
        }
        CircleMembership::factory()->rejected()->create([
            'user_id' => $adherents->get(6)->id, 'circle_id' => $mobilite->id,
        ]);

        // Adhérents dans Finances
        foreach ($adherents->slice(3, 4) as $user) {
            CircleMembership::factory()->approved()->create([
                'user_id' => $user->id, 'circle_id' => $finance->id,
            ]);
        }
        CircleMembership::factory()->pending()->create([
            'user_id' => $adherents->get(8)->id, 'circle_id' => $finance->id,
        ]);

        // Adhérents dans Pilotage
        foreach ($adherents->slice(0, 3) as $user) {
            CircleMembership::factory()->approved()->create([
                'user_id' => $user->id, 'circle_id' => $pilotage->id,
            ]);
        }

        // Adhérents dans Lab
        CircleMembership::factory()->approved()->create([
            'user_id' => $referent1->id, 'circle_id' => $lab->id,
        ]);
        foreach ($adherents->slice(6, 4) as $user) {
            CircleMembership::factory()->approved()->create([
                'user_id' => $user->id, 'circle_id' => $lab->id,
            ]);
        }

        // Adhérent dans Comms
        CircleMembership::factory()->approved()->create([
            'user_id' => $adherents->get(10)->id, 'circle_id' => $comms->id,
        ]);
        CircleMembership::factory()->pending()->create([
            'user_id' => $adherents->get(11)->id, 'circle_id' => $comms->id,
        ]);

        // ══════════════════════════════════════════════════════════════
        // 4. POSTS / PUBLICATIONS
        // ══════════════════════════════════════════════════════════════

        // Non publiés au fil général
        Post::factory()->count(3)->create([
            'circle_id' => $mobilite->id, 'author_id' => $referent1->id,
        ]);
        // Publiés au fil général
        Post::factory()->pushed()->count(2)->create([
            'circle_id' => $pilotage->id, 'author_id' => $referent3->id,
        ]);
        Post::factory()->pushed()->create([
            'circle_id' => $finance->id, 'author_id' => $referent2->id,
        ]);
        Post::factory()->count(2)->create([
            'circle_id' => $lab->id, 'author_id' => $referent1->id,
        ]);
        Post::factory()->pushed()->create([
            'circle_id' => $comms->id, 'author_id' => $referent3->id,
        ]);

        // ══════════════════════════════════════════════════════════════
        // 5. ÉVÉNEMENTS — tous les cas
        // ══════════════════════════════════════════════════════════════

        // Futur public avec durée + inscriptions
        $eventPublicFutur = Event::factory()->public()->withEnd()->create([
            'circle_id' => $mobilite->id,
            'author_id' => $referent1->id,
            'title' => 'Forum Mobilité Durable',
            'starts_at' => now()->addDays(14),
            'is_public' => true,
            'tag' => 'forum',
            'foot_type' => 'transport',
            'location' => 'Place du Village',
        ]);
        // Inscriptions membres
        foreach ($adherents->take(3) as $user) {
            EventRegistration::create([
                'event_id' => $eventPublicFutur->id,
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }
        // Inscription anonyme
        EventRegistration::create([
            'event_id' => $eventPublicFutur->id,
            'user_id' => null,
            'name' => 'Visiteur Anonyme',
            'email' => 'visiteur.anon@example.com',
        ]);

        // Futur public sans inscription
        Event::factory()->public()->create([
            'circle_id' => $pilotage->id,
            'author_id' => $referent3->id,
            'title' => 'AG Annuelle 2026',
            'starts_at' => now()->addDays(30),
            'is_public' => true,
            'tag' => 'ag',
        ]);

        // Futur privé avec durée
        Event::factory()->withEnd()->create([
            'circle_id' => $finance->id,
            'author_id' => $referent2->id,
            'title' => 'Réunion Budget Q3',
            'starts_at' => now()->addDays(7),
            'location' => 'Siège association',
        ]);

        // Futur privé sans durée
        Event::factory()->create([
            'circle_id' => $intergen->id,
            'author_id' => $referent1->id,
            'title' => 'Café intergénérationnel',
            'starts_at' => now()->addDays(5),
        ]);

        // Passé privé
        Event::factory()->past()->create([
            'circle_id' => $mobilite->id,
            'author_id' => $referent1->id,
            'title' => 'Atelier Covoiturage',
            'starts_at' => now()->subDays(10),
        ]);

        // Passé public
        Event::factory()->past()->public()->create([
            'circle_id' => $lab->id,
            'author_id' => $referent1->id,
            'title' => 'Hackathon Innovation Sociale',
            'starts_at' => now()->subDays(20),
            'is_public' => true,
            'tag' => 'hackathon',
        ]);

        // ══════════════════════════════════════════════════════════════
        // 6. RÉUNIONS + ORDRES DU JOUR + COMPTES-RENDUS
        // ══════════════════════════════════════════════════════════════

        // Passée publique — CR publié
        $meetingPast = Meeting::factory()->past()->create([
            'circle_id' => $mobilite->id,
            'created_by' => $referent1->id,
            'title' => 'Réunion mensuelle Mobilité — Mai 2026',
            'scheduled_at' => now()->subDays(7),
            'duration_minutes' => 90,
            'location' => 'Salle Mairie',
            'is_public' => true,
        ]);
        foreach (range(1, 3) as $i) {
            MeetingAgendaItem::factory()->create([
                'meeting_id' => $meetingPast->id,
                'position' => $i,
                'title' => ['Tour de table', 'Avancement covoiturage', 'Prochaines actions'][$i - 1],
                'duration_minutes' => 30,
            ]);
        }
        MeetingReport::factory()->published()->create([
            'meeting_id' => $meetingPast->id,
            'created_by' => $referent1->id,
            'participants' => 'Marie Dupont, Alice, Bob, Charlie, David',
            'agenda_notes' => [
                ['point' => 'Tour de table', 'note' => 'Présentation des nouveaux membres.'],
                ['point' => 'Covoiturage',   'note' => '42 trajets ce mois, objectif dépassé.'],
            ],
            'decisions' => [
                ['text' => 'Validation du budget covoiturage Q3'],
                ['text' => 'Lancement enquête mobilité douce'],
            ],
            'open_points' => [
                ['text' => 'Partenariat avec collectivité — à finaliser avant le 15 juin'],
            ],
            'free_notes' => 'Excellente dynamique. Prochain RDV le 3 juin.',
        ]);

        // Passée privée — CR brouillon
        $meetingPast2 = Meeting::factory()->past()->create([
            'circle_id' => $finance->id,
            'created_by' => $referent2->id,
            'title' => 'Réunion Finances — Avril 2026',
            'scheduled_at' => now()->subDays(15),
            'is_public' => false,
        ]);
        MeetingAgendaItem::factory()->create([
            'meeting_id' => $meetingPast2->id,
            'position' => 1,
            'title' => 'Présentation bilan comptable S1',
        ]);
        MeetingAgendaItem::factory()->create([
            'meeting_id' => $meetingPast2->id,
            'position' => 2,
            'title' => 'Discussion budget événement annuel',
        ]);
        MeetingReport::factory()->draft()->create([
            'meeting_id' => $meetingPast2->id,
            'created_by' => $referent2->id,
            'decisions' => [['text' => 'Gel des dépenses non essentielles en juillet']],
        ]);

        // Passée — sans CR
        Meeting::factory()->past()->create([
            'circle_id' => $pilotage->id,
            'created_by' => $referent3->id,
            'title' => 'Comité stratégique — Mars 2026',
        ]);

        // Future publique — avec agenda — visio
        $meetingFutur = Meeting::factory()->create([
            'circle_id' => $mobilite->id,
            'created_by' => $referent1->id,
            'title' => 'Réunion Mobilité — Juin 2026',
            'scheduled_at' => now()->addDays(10),
            'duration_minutes' => 60,
            'visio_url' => 'https://meet.example.com/mobilite-juin',
            'is_public' => true,
        ]);
        foreach (range(1, 3) as $i) {
            MeetingAgendaItem::factory()->create([
                'meeting_id' => $meetingFutur->id,
                'position' => $i,
                'title' => ['Bilan mai', 'Préparation Forum Mobilité', 'Divers'][$i - 1],
                'duration_minutes' => 20,
            ]);
        }

        // Future privée — sans agenda
        Meeting::factory()->create([
            'circle_id' => $lab->id,
            'created_by' => $referent1->id,
            'title' => 'Kick-off nouveau projet Lab',
            'scheduled_at' => now()->addDays(21),
        ]);

        // ══════════════════════════════════════════════════════════════
        // 7. ACTIONS DES CERCLES — todo / in_progress / done
        // ══════════════════════════════════════════════════════════════
        CircleAction::factory()->create([
            'circle_id' => $mobilite->id, 'author_id' => $referent1->id,
            'status' => CircleActionStatus::Todo,
            'title' => 'Préparer sondage mobilités douces',
        ]);
        CircleAction::factory()->inProgress()->create([
            'circle_id' => $mobilite->id, 'author_id' => $referent1->id,
            'title' => 'Rédiger guide covoiturage',
        ]);
        CircleAction::factory()->done()->create([
            'circle_id' => $mobilite->id, 'author_id' => $referent1->id,
            'title' => 'Publier carte des parkings vélo',
        ]);
        CircleAction::factory()->create([
            'circle_id' => $finance->id, 'author_id' => $referent2->id,
            'title' => 'Clôturer exercice comptable',
        ]);
        CircleAction::factory()->inProgress()->create([
            'circle_id' => $pilotage->id, 'author_id' => $referent3->id,
        ]);
        CircleAction::factory()->done()->create([
            'circle_id' => $lab->id, 'author_id' => $referent1->id,
        ]);

        // ══════════════════════════════════════════════════════════════
        // 8. DOCUMENTS DES CERCLES — PDF / lien / tags divers
        // ══════════════════════════════════════════════════════════════
        CircleDocument::factory()->asPdf()->withTags(['statuts', 'rapport'])->create([
            'circle_id' => $mobilite->id, 'uploaded_by' => $referent1->id,
            'title' => 'Rapport annuel Mobilité 2025',
        ]);
        CircleDocument::factory()->asPdf()->withTags(['réunion', 'compte-rendu'])->create([
            'circle_id' => $finance->id, 'uploaded_by' => $referent2->id,
            'title' => 'PV Réunion Finances Avril',
        ]);
        CircleDocument::factory()->asPdf()->withTags([])->create([
            'circle_id' => $mobilite->id, 'uploaded_by' => $referent1->id,
            'title' => 'Charte covoiturage (sans tag)',
        ]);
        CircleDocument::factory()->asLink()->create([
            'circle_id' => $pilotage->id, 'uploaded_by' => $referent3->id,
            'title' => 'Tableau de bord stratégique (lien externe)',
        ]);
        CircleDocument::factory()->asLink()->create([
            'circle_id' => $lab->id, 'uploaded_by' => $referent1->id,
            'title' => 'Ressources facilitation (Notion)',
        ]);

        // ══════════════════════════════════════════════════════════════
        // 9. JOURNAL DES CERCLES
        // ══════════════════════════════════════════════════════════════
        CircleJournalEntry::factory()->today()->create([
            'circle_id' => $mobilite->id, 'created_by' => $referent1->id,
            'title' => 'Bilan du mois de mai',
        ]);
        CircleJournalEntry::factory()->past()->create([
            'circle_id' => $mobilite->id, 'created_by' => $referent1->id,
            'title' => 'Rétrospective covoiturage Q1',
        ]);
        CircleJournalEntry::factory()->create([
            'circle_id' => $finance->id, 'created_by' => $referent2->id,
        ]);
        CircleJournalEntry::factory()->past()->create([
            'circle_id' => $lab->id, 'created_by' => $referent1->id,
        ]);

        // ══════════════════════════════════════════════════════════════
        // 10. SONDAGES (POLLS)
        // ══════════════════════════════════════════════════════════════

        // Oui/Non ouvert — votes mixtes
        $pollYesNoOpen = Poll::factory()->yesNo()->open()->create([
            'circle_id' => $mobilite->id,
            'created_by' => $referent1->id,
            'title' => 'Souhaitez-vous organiser un événement vélo en juin ?',
        ]);
        foreach ($adherents->take(5) as $idx => $user) {
            PollVote::factory()->create([
                'poll_id' => $pollYesNoOpen->id,
                'user_id' => $user->id,
                'choice' => $idx % 2 === 0 ? 'oui' : 'non',
            ]);
        }
        PollVote::factory()->create([
            'poll_id' => $pollYesNoOpen->id,
            'user_id' => $referent1->id,
            'choice' => 'oui',
        ]);

        // Oui/Non fermé — avec votes
        $pollYesNoClosed = Poll::factory()->yesNo()->closed()->create([
            'circle_id' => $finance->id,
            'created_by' => $referent2->id,
            'title' => 'Approuvez-vous le rapport financier 2025 ?',
        ]);
        foreach ($adherents->slice(3, 4) as $user) {
            PollVote::factory()->create([
                'poll_id' => $pollYesNoClosed->id,
                'user_id' => $user->id,
                'choice' => 'oui',
            ]);
        }
        PollVote::factory()->create([
            'poll_id' => $pollYesNoClosed->id,
            'user_id' => $adherents->get(8)->id,
            'choice' => 'non',
        ]);

        // Choix multiple ouvert
        $pollMultiOpen = Poll::factory()->multiple()->open()->create([
            'circle_id' => $pilotage->id,
            'created_by' => $referent3->id,
            'title' => 'Quels thèmes pour la prochaine AG ?',
            'options' => ['Gouvernance', 'Finance participative', 'Projets 2027', 'Communication externe'],
        ]);
        $opts = ['Gouvernance', 'Finance participative', 'Projets 2027', 'Communication externe'];
        foreach ($adherents->take(4) as $idx => $user) {
            PollVote::factory()->create([
                'poll_id' => $pollMultiOpen->id,
                'user_id' => $user->id,
                'choice' => $opts[$idx],
            ]);
        }

        // Choix multiple fermé — sans votes (cas limite)
        Poll::factory()->multiple()->closed()->create([
            'circle_id' => $lab->id,
            'created_by' => $referent1->id,
            'title' => 'Quel outil de gestion de projet ?',
            'options' => ['Notion', 'Trello', 'Excel', 'Autre'],
        ]);

        // Sondage global (sans cercle) — très vote
        $pollGlobal = Poll::factory()->yesNo()->open()->create([
            'circle_id' => null,
            'created_by' => $referent3->id,
            'title' => 'Êtes-vous favorable au nouveau règlement intérieur ?',
        ]);
        foreach ($adherents->take(8) as $idx => $user) {
            PollVote::factory()->create([
                'poll_id' => $pollGlobal->id,
                'user_id' => $user->id,
                'choice' => $idx < 6 ? 'oui' : 'non',
            ]);
        }

        // Sondage global fermé — sans votes (cas zéro participation)
        Poll::factory()->yesNo()->closed()->create([
            'circle_id' => null,
            'created_by' => $admin->id,
            'title' => 'Validez-vous le calendrier 2026 ?',
        ]);

        // ══════════════════════════════════════════════════════════════
        // 11. SCRUTINS — tous les statuts × toutes combinaisons
        // ══════════════════════════════════════════════════════════════

        // 11a. Brouillon (2 options, pas de votes)
        $scrutinDraft = Scrutin::factory()->draft()->create([
            'title' => 'Élection du bureau — Brouillon',
            'description' => 'Scrutin en cours de préparation, pas encore ouvert.',
            'created_by' => $admin->id,
        ]);
        ScrutinOption::factory()->create(['scrutin_id' => $scrutinDraft->id, 'label' => 'Liste A — Renouveau', 'position' => 1]);
        ScrutinOption::factory()->create(['scrutin_id' => $scrutinDraft->id, 'label' => 'Liste B — Continuité', 'position' => 2]);

        // 11b. Ouvert — majorité simple / quorum fixe / votes partiels
        $scrutinOpen = Scrutin::factory()->open()->create([
            'title' => 'Adoption des nouveaux statuts',
            'description' => 'Soumis à l\'ensemble des adhérents. Majorité simple requise.',
            'quorum_type' => ScrutinQuorumType::Fixed,
            'quorum_value' => 5,
            'majority_type' => ScrutinMajorityType::Simple,
            'created_by' => $admin->id,
        ]);
        $optPour = ScrutinOption::factory()->create(['scrutin_id' => $scrutinOpen->id, 'label' => 'Pour',       'position' => 1]);
        $optContre = ScrutinOption::factory()->create(['scrutin_id' => $scrutinOpen->id, 'label' => 'Contre',    'position' => 2]);
        $optAbst = ScrutinOption::factory()->create(['scrutin_id' => $scrutinOpen->id, 'label' => 'Abstention', 'position' => 3]);
        foreach ($adherents->take(6) as $idx => $user) {
            ScrutinVote::factory()->create([
                'scrutin_id' => $scrutinOpen->id,
                'scrutin_option_id' => $idx < 4 ? $optPour->id : ($idx === 4 ? $optContre->id : $optAbst->id),
                'user_id' => $user->id,
            ]);
        }

        // 11c. Ouvert — majorité qualifiée 2/3 / quorum proportionnel 30 %
        $scrutinQualif = Scrutin::factory()->open()->qualified(66.67)->proportional(30.0)->create([
            'title' => 'Modification du règlement intérieur',
            'description' => 'Majorité qualifiée aux 2/3 requise. Quorum : 30 % des membres actifs.',
            'created_by' => $admin->id,
        ]);
        $optQ1 = ScrutinOption::factory()->create(['scrutin_id' => $scrutinQualif->id, 'label' => 'Adopter', 'position' => 1]);
        $optQ2 = ScrutinOption::factory()->create(['scrutin_id' => $scrutinQualif->id, 'label' => 'Rejeter', 'position' => 2]);
        foreach ($adherents->take(4) as $user) {
            ScrutinVote::factory()->create([
                'scrutin_id' => $scrutinQualif->id,
                'scrutin_option_id' => $optQ1->id,
                'user_id' => $user->id,
            ]);
        }
        ScrutinVote::factory()->create([
            'scrutin_id' => $scrutinQualif->id,
            'scrutin_option_id' => $optQ2->id,
            'user_id' => $adherents->get(4)->id,
        ]);

        // 11d. Clôturé — résultats nets
        $scrutinClosed = Scrutin::factory()->closed()->create([
            'title' => 'Adhésion au réseau régional',
            'description' => 'Scrutin clôturé. Le OUI l\'emporte.',
            'created_by' => $admin->id,
        ]);
        $optC1 = ScrutinOption::factory()->create(['scrutin_id' => $scrutinClosed->id, 'label' => 'Oui', 'position' => 1]);
        $optC2 = ScrutinOption::factory()->create(['scrutin_id' => $scrutinClosed->id, 'label' => 'Non', 'position' => 2]);
        foreach ($adherents->take(8) as $idx => $user) {
            ScrutinVote::factory()->create([
                'scrutin_id' => $scrutinClosed->id,
                'scrutin_option_id' => $idx < 6 ? $optC1->id : $optC2->id,
                'user_id' => $user->id,
            ]);
        }
        ScrutinVote::factory()->create([
            'scrutin_id' => $scrutinClosed->id,
            'scrutin_option_id' => $optC1->id,
            'user_id' => $referent2->id,
        ]);

        // 11e. Clôturé — quorum non atteint (peu de votes)
        $scrutinClosedNoQuorum = Scrutin::factory()->closed()->create([
            'title' => 'Validation du partenariat — quorum non atteint',
            'quorum_type' => ScrutinQuorumType::Fixed,
            'quorum_value' => 20,
            'created_by' => $admin->id,
        ]);
        $optN1 = ScrutinOption::factory()->create(['scrutin_id' => $scrutinClosedNoQuorum->id, 'label' => 'Oui', 'position' => 1]);
        ScrutinOption::factory()->create(['scrutin_id' => $scrutinClosedNoQuorum->id, 'label' => 'Non', 'position' => 2]);
        foreach ($adherents->take(2) as $user) {
            ScrutinVote::factory()->create([
                'scrutin_id' => $scrutinClosedNoQuorum->id,
                'scrutin_option_id' => $optN1->id,
                'user_id' => $user->id,
            ]);
        }

        // 11f. Annulé
        Scrutin::factory()->cancelled()->create([
            'title' => 'Élection annulée — délai dépassé',
            'description' => 'Ce scrutin a été annulé avant ouverture.',
            'created_by' => $admin->id,
        ]);

        // ══════════════════════════════════════════════════════════════
        // 12. CONSULTATIONS — tous les modes × statuts
        // ══════════════════════════════════════════════════════════════

        // Avis libre ouvert — réponses numériques + terrain + masquée
        $consultAvis = Consultation::factory()->ouverte()->create([
            'titre' => 'Quelles priorités pour 2027 ?',
            'description' => 'Donnez votre avis sur les orientations de l\'association.',
            'mode_recueil' => ConsultationMode::AvisLibre,
            'masque' => false,
        ]);
        ConsultationReponse::factory()->count(4)->create(['consultation_id' => $consultAvis->id]);
        ConsultationReponse::factory()->terrain()->create(['consultation_id' => $consultAvis->id]);
        ConsultationReponse::factory()->masquee()->create(['consultation_id' => $consultAvis->id]);

        // Avis libre clôturé
        $consultAvisClos = Consultation::factory()->cloturee()->create([
            'titre' => 'Retour sur l\'événement de mars',
            'mode_recueil' => ConsultationMode::AvisLibre,
        ]);
        ConsultationReponse::factory()->count(6)->create(['consultation_id' => $consultAvisClos->id]);
        ConsultationReponse::factory()->terrain()->count(2)->create(['consultation_id' => $consultAvisClos->id]);

        // Avis libre ouvert — zéro réponse (cas vide)
        Consultation::factory()->ouverte()->create([
            'titre' => 'Idées pour animer le quartier',
            'mode_recueil' => ConsultationMode::AvisLibre,
        ]);

        // Pétition / signature ouverte
        $consultSign = Consultation::factory()->ouverte()->signature()->create([
            'titre' => 'Pétition : maintien du bus de nuit B22',
            'description' => 'Signez pour demander le maintien de la ligne.',
        ]);
        foreach (range(1, 15) as $i) {
            ConsultationReponse::factory()->create([
                'consultation_id' => $consultSign->id,
                'mode' => ConsultationMode::Signature->value,
                'contenu' => 'Je signe cette pétition.',
            ]);
        }
        ConsultationReponse::factory()->terrain()->create([
            'consultation_id' => $consultSign->id,
            'mode' => ConsultationMode::Signature->value,
            'contenu' => 'Je signe (collecte terrain).',
        ]);

        // Pétition clôturée
        $consultSignClos = Consultation::factory()->cloturee()->signature()->create([
            'titre' => 'Pétition : création d\'une piste cyclable — Clôturée',
        ]);
        ConsultationReponse::factory()->count(32)->create([
            'consultation_id' => $consultSignClos->id,
            'mode' => ConsultationMode::Signature->value,
            'contenu' => 'Je signe.',
        ]);

        // Vote indicatif ouvert
        $consultVote = Consultation::factory()->ouverte()->voteIndicatif()->create([
            'titre' => 'Lieu pour la prochaine AG',
            'description' => 'Vote indicatif non contraignant.',
            'options' => ['Salle des fêtes', 'Centre culturel', 'En ligne'],
        ]);
        foreach (['Salle des fêtes', 'Centre culturel', 'En ligne', 'Salle des fêtes', 'En ligne', 'Centre culturel'] as $opt) {
            ConsultationReponse::factory()->create([
                'consultation_id' => $consultVote->id,
                'mode' => ConsultationMode::VoteIndicatif->value,
                'contenu' => $opt,
            ]);
        }

        // Vote indicatif clôturé — masqué
        $consultVoteClos = Consultation::factory()->cloturee()->voteIndicatif()->create([
            'titre' => 'Priorité rénovation — résultats masqués',
            'masque' => true,
            'options' => ['Toiture', 'Fenêtres', 'Isolation'],
        ]);
        foreach (['Toiture', 'Fenêtres', 'Isolation', 'Toiture', 'Toiture'] as $opt) {
            ConsultationReponse::factory()->create([
                'consultation_id' => $consultVoteClos->id,
                'mode' => ConsultationMode::VoteIndicatif->value,
                'contenu' => $opt,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 13. SERVICES LAB
        // ══════════════════════════════════════════════════════════════
        $service1 = LabService::factory()->create([
            'title' => 'Facilitation de réunion',
            'category' => 'Facilitation',
            'created_by' => $referent1->id,
        ]);
        $service2 = LabService::factory()->create([
            'title' => 'Atelier Design Thinking',
            'category' => 'Innovation',
            'created_by' => $referent1->id,
        ]);
        $service3 = LabService::factory()->create([
            'title' => 'Accompagnement gestion de projet',
            'category' => 'Gestion de projet',
            'created_by' => $referent2->id,
        ]);
        LabService::factory()->count(2)->create();

        // ══════════════════════════════════════════════════════════════
        // 14. DEMANDES INTERNES AU LAB — tous statuts
        // ══════════════════════════════════════════════════════════════
        LabInternalRequest::factory()->create([
            'circle_id' => $mobilite->id,
            'user_id' => $adherents->get(0)->id,
            'lab_service_id' => $service1->id,
            'status' => LabRequestStatus::Nouvelle,
            'desired_date' => now()->addDays(20)->format('Y-m-d'),
        ]);
        LabInternalRequest::factory()->enCours()->create([
            'circle_id' => $finance->id,
            'user_id' => $adherents->get(3)->id,
            'lab_service_id' => $service2->id,
        ]);
        LabInternalRequest::factory()->traitee()->create([
            'circle_id' => $pilotage->id,
            'user_id' => $referent3->id,
        ]);
        LabInternalRequest::factory()->create([
            'circle_id' => $lab->id,
            'user_id' => $adherents->get(7)->id,
            'lab_service_id' => $service3->id,
            'status' => LabRequestStatus::Nouvelle,
        ]);
        LabInternalRequest::factory()->enCours()->create([
            'circle_id' => $comms->id,
            'user_id' => $adherents->get(10)->id,
        ]);

        // ══════════════════════════════════════════════════════════════
        // 15. DEMANDES EXTERNES AU LAB — citoyen / entreprise × statuts
        // ══════════════════════════════════════════════════════════════

        // Citoyen — nouvelle
        LabExternalRequest::factory()->citoyen()->create([
            'statut' => LabRequestStatus::Nouvelle,
            'type_projet' => 'Mobilité douce',
            'thematique' => 'Transport',
        ]);
        // Citoyen — en cours
        LabExternalRequest::factory()->citoyen()->enCours()->create([
            'type_projet' => 'Agriculture urbaine',
            'thematique' => 'Alimentation',
        ]);
        // Citoyen — traitée
        LabExternalRequest::factory()->citoyen()->traitee()->create();

        // Entreprise — nouvelle
        LabExternalRequest::factory()->entreprise()->create([
            'statut' => LabRequestStatus::Nouvelle,
            'fonction' => 'Directeur RSE',
            'taille_organisation' => '50-200',
            'thematique' => 'Innovation sociale',
        ]);
        // Entreprise — en cours
        LabExternalRequest::factory()->entreprise()->enCours()->create([
            'fonction' => 'Chef de projet',
            'taille_organisation' => '200+',
            'thematique' => 'Numérique responsable',
        ]);
        // Entreprise — traitée
        LabExternalRequest::factory()->entreprise()->traitee()->create([
            'fonction' => 'DRH',
            'taille_organisation' => '10-50',
        ]);

        // ══════════════════════════════════════════════════════════════
        // 16. OUTILS LAB — actif / inactif
        // ══════════════════════════════════════════════════════════════
        LabTool::factory()->create([
            'title' => 'Canvas Projet',
            'category' => 'Design Thinking',
            'active' => true,
            'created_by' => $referent1->id,
        ]);
        LabTool::factory()->create([
            'title' => '30 Icebreakers',
            'category' => 'Facilitation',
            'active' => true,
            'created_by' => $referent1->id,
        ]);
        LabTool::factory()->create([
            'title' => 'Matrice d\'Eisenhower',
            'category' => 'Idéation',
            'active' => true,
        ]);
        LabTool::factory()->inactive()->create([
            'title' => '[Archivé] Ancien template réunion',
            'active' => false,
        ]);
        LabTool::factory()->count(3)->create();
    }
}
