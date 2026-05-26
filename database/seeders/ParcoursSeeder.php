<?php

namespace Database\Seeders;

use App\Enums\ParcoursCtaType;
use App\Models\ParcoursOption;
use App\Models\ParcoursQuestion;
use App\Models\ParcoursService;
use Illuminate\Database\Seeder;

class ParcoursSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------------------------
        // 1. Services (9 fiches)
        // -------------------------------------------------------------------------

        $servicesData = [
            'co-developpement' => [
                'name'             => 'Co-développement',
                'branche'          => 'Explorer / Construire',
                'description'      => 'Testez et affinez votre idée avec des pairs avant de vous lancer.',
                'pour_qui'         => 'Porteurs de projet individuels, petits collectifs',
                'use_cases'        => [
                    'Vous avez une idée mais ne savez pas si elle tient la route',
                    'Vous voulez des retours structurés sans exposer votre projet publiquement',
                    'Vous cherchez à progresser en échangeant avec d\'autres porteurs',
                ],
                'ce_que_ca_produit'=> 'Idée affinée, points de blocage identifiés, réseau de pairs',
                'format'           => 'Sessions en groupe, 2h, format régulier ou ponctuel',
                'cta_type'         => ParcoursCtaType::Contact,
                'cta_value'        => 'contact@lafabrique-benfeld.fr',
                'is_active'        => true,
                'sort_order'       => 10,
            ],
            'design-thinking' => [
                'name'             => 'Design Thinking',
                'branche'          => 'Explorer',
                'description'      => 'Comprenez votre problème en profondeur avant de chercher une solution.',
                'pour_qui'         => 'Équipes, organisations, porteurs confrontés à une situation complexe',
                'use_cases'        => [
                    'Vous n\'arrivez pas à définir précisément le problème à résoudre',
                    'Vos solutions passées n\'ont pas fonctionné et vous voulez repartir de l\'usage',
                    'Vous devez convaincre des parties prenantes d\'un diagnostic commun',
                ],
                'ce_que_ca_produit'=> 'Reformulation du problème, insights utilisateurs, premières pistes validées',
                'format'           => 'Atelier 1 à 2 jours',
                'cta_type'         => ParcoursCtaType::Contact,
                'cta_value'        => 'contact@lafabrique-benfeld.fr',
                'is_active'        => true,
                'sort_order'       => 20,
            ],
            'co-design' => [
                'name'             => 'Co-design',
                'branche'          => 'Explorer / Construire',
                'description'      => 'Concevez votre projet avec les personnes directement concernées.',
                'pour_qui'         => 'Porteurs de projet à impact, organisations souhaitant impliquer des parties prenantes',
                'use_cases'        => [
                    'Vous concevez un service ou un espace et voulez impliquer les futurs usagers',
                    'Vous avez besoin de légitimité externe pour votre projet',
                    'Vous voulez éviter de concevoir pour des gens plutôt qu\'avec eux',
                ],
                'ce_que_ca_produit'=> 'Prototype co-conçu, adhésion des parties prenantes, documentation du processus',
                'format'           => 'Atelier(s) sur mesure, 1 à 3 jours',
                'cta_type'         => ParcoursCtaType::Contact,
                'cta_value'        => 'contact@lafabrique-benfeld.fr',
                'is_active'        => true,
                'sort_order'       => 30,
            ],
            'montage-projet' => [
                'name'             => 'Montage de projet',
                'branche'          => 'Construire / Accélérer',
                'description'      => 'Passez de l\'idée à un dossier solide, finançable et partenarial.',
                'pour_qui'         => 'Porteurs de projets à impact cherchant financements ou partenaires',
                'use_cases'        => [
                    'Vous avez un projet mais n\'avez pas encore de dossier formalisé',
                    'Vous devez répondre à un appel à projets et ne savez pas par où commencer',
                    'Vous cherchez à structurer votre modèle économique ou vos sources de financement',
                ],
                'ce_que_ca_produit'=> 'Dossier de candidature, budget prévisionnel, cartographie partenaires',
                'format'           => 'Accompagnement individuel, 4 à 8 semaines',
                'cta_type'         => ParcoursCtaType::Contact,
                'cta_value'        => 'contact@lafabrique-benfeld.fr',
                'is_active'        => true,
                'sort_order'       => 40,
            ],
            'gestion-projet' => [
                'name'             => 'Gestion de projet',
                'branche'          => 'Accélérer',
                'description'      => 'Remettez votre projet sur les rails et livrez ce que vous avez promis.',
                'pour_qui'         => 'Porteurs de projets en cours de réalisation rencontrant des difficultés d\'exécution',
                'use_cases'        => [
                    'Votre projet est lancé mais les jalons ne sont pas tenus',
                    'Vous gérez plusieurs parties prenantes et perdez le fil',
                    'Vous avez besoin d\'outils et de méthodes pour piloter sans vous noyer',
                ],
                'ce_que_ca_produit'=> 'Plan d\'action révisé, outils de suivi, répartition des responsabilités clarifiée',
                'format'           => 'Accompagnement, 2 à 6 semaines',
                'cta_type'         => ParcoursCtaType::Contact,
                'cta_value'        => 'contact@lafabrique-benfeld.fr',
                'is_active'        => true,
                'sort_order'       => 50,
            ],
            'entrepreneuriat' => [
                'name'             => 'Entrepreneuriat',
                'branche'          => 'Construire',
                'description'      => 'Créez ou développez votre activité avec méthode et sans vous perdre.',
                'pour_qui'         => 'Entrepreneurs en création ou en développement d\'activité économique',
                'use_cases'        => [
                    'Vous lancez votre activité et ne savez pas dans quel ordre traiter les sujets',
                    'Vous avez une activité existante et cherchez à la développer',
                    'Vous hésitez entre plusieurs modèles économiques',
                ],
                'ce_que_ca_produit'=> 'Modèle économique clarifié, plan d\'action 90 jours, compréhension du marché',
                'format'           => 'Accompagnement individuel ou collectif, 6 à 12 semaines',
                'cta_type'         => ParcoursCtaType::Contact,
                'cta_value'        => 'contact@lafabrique-benfeld.fr',
                'is_active'        => true,
                'sort_order'       => 60,
            ],
            'seminaire-ic' => [
                'name'             => 'Séminaire Intelligence Collective',
                'branche'          => 'Explorer / Accélérer',
                'description'      => 'Faites travailler votre équipe ensemble sur ce qui compte vraiment.',
                'pour_qui'         => 'Organisations, équipes, structures souhaitant traiter un enjeu collectif',
                'use_cases'        => [
                    'Votre équipe n\'est pas alignée sur les priorités ou la direction',
                    'Vous avez besoin de produire ensemble une vision ou une stratégie',
                    'Vous traversez un changement et voulez maintenir la cohésion',
                ],
                'ce_que_ca_produit'=> 'Décisions collectives documentées, engagement de l\'équipe, plan d\'action partagé',
                'format'           => 'Séminaire 1 à 2 jours, sur site ou hors les murs',
                'cta_type'         => ParcoursCtaType::Contact,
                'cta_value'        => 'contact@lafabrique-benfeld.fr',
                'is_active'        => true,
                'sort_order'       => 70,
            ],
            'strategie' => [
                'name'             => 'Stratégie',
                'branche'          => 'Explorer / Accélérer',
                'description'      => 'Définissez ou redéfinissez votre cap et les priorités pour y arriver.',
                'pour_qui'         => 'Dirigeants, fondateurs, responsables de structures souhaitant pivoter ou clarifier leur direction',
                'use_cases'        => [
                    'Vous n\'êtes plus certain que votre direction actuelle soit la bonne',
                    'Vous devez prendre une décision structurante et manquez de recul',
                    'Vous voulez aligner votre équipe autour d\'une vision claire',
                ],
                'ce_que_ca_produit'=> 'Axes stratégiques clarifiés, feuille de route, arbitrages documentés',
                'format'           => 'Accompagnement 1 à 3 jours, individuel ou comité',
                'cta_type'         => ParcoursCtaType::Contact,
                'cta_value'        => 'contact@lafabrique-benfeld.fr',
                'is_active'        => true,
                'sort_order'       => 80,
            ],
            'facilitation' => [
                'name'             => 'Facilitation',
                'branche'          => 'Explorer',
                'description'      => 'Animez vos réunions et ateliers pour que chaque voix compte et que les décisions avancent.',
                'pour_qui'         => 'Équipes, associations, porteurs souhaitant animer des temps collectifs efficaces',
                'use_cases'        => [
                    'Vos réunions s\'éternisent sans produire de décisions claires',
                    'Certains participants ne s\'expriment pas et vous perdez leur perspective',
                    'Vous avez besoin d\'un tiers neutre pour animer un sujet sensible',
                ],
                'ce_que_ca_produit'=> 'Compte-rendu structuré, décisions actées, dynamique de groupe renforcée',
                'format'           => 'Demi-journée ou journée, sur mesure',
                'cta_type'         => ParcoursCtaType::Contact,
                'cta_value'        => 'contact@lafabrique-benfeld.fr',
                'is_active'        => true,
                'sort_order'       => 90,
            ],
        ];

        foreach ($servicesData as $slug => $data) {
            ParcoursService::firstOrCreate(
                ['slug' => $slug],
                array_merge(['slug' => $slug], $data)
            );
        }

        // -------------------------------------------------------------------------
        // 2. Questions (11 entrées)
        // -------------------------------------------------------------------------

        $questionsData = [
            'q0' => [
                'label'              => 'Qu\'est-ce qui vous amène aujourd\'hui ?',
                'is_root'            => true,
                'show_fallback_link' => false,
                'sort_order'         => 0,
            ],
            'q1a' => [
                'label'              => 'Vous travaillez plutôt…',
                'is_root'            => false,
                'show_fallback_link' => false,
                'sort_order'         => 10,
            ],
            'q1b-solo' => [
                'label'              => 'Ce que vous cherchez (en tant que solo ou petit collectif) :',
                'is_root'            => false,
                'show_fallback_link' => true,
                'sort_order'         => 20,
            ],
            'q1b-org' => [
                'label'              => 'Ce que vous cherchez (au sein de votre organisation) :',
                'is_root'            => false,
                'show_fallback_link' => true,
                'sort_order'         => 30,
            ],
            'q2a' => [
                'label'              => 'Votre idée concerne…',
                'is_root'            => false,
                'show_fallback_link' => false,
                'sort_order'         => 40,
            ],
            'q2b-impact' => [
                'label'              => 'Ce dont vous avez besoin (projet à impact) :',
                'is_root'            => false,
                'show_fallback_link' => true,
                'sort_order'         => 50,
            ],
            'q2b-eco' => [
                'label'              => 'Ce dont vous avez besoin (activité économique) :',
                'is_root'            => false,
                'show_fallback_link' => true,
                'sort_order'         => 60,
            ],
            'q3a' => [
                'label'              => 'Où en êtes-vous ?',
                'is_root'            => false,
                'show_fallback_link' => false,
                'sort_order'         => 70,
            ],
            'q3b-mise-en-oeuvre' => [
                'label'              => 'Le principal problème :',
                'is_root'            => false,
                'show_fallback_link' => true,
                'sort_order'         => 80,
            ],
            'q3b-alignement' => [
                'label'              => 'Ce que vous cherchez (alignement) :',
                'is_root'            => false,
                'show_fallback_link' => true,
                'sort_order'         => 90,
            ],
        ];

        // Ensure at most one root question
        ParcoursQuestion::where('is_root', true)->update(['is_root' => false]);

        $questionModels = [];
        foreach ($questionsData as $key => $data) {
            $questionModels[$key] = ParcoursQuestion::firstOrCreate(
                ['label' => $data['label']],
                $data
            );
            // Sync flags on existing records too
            $questionModels[$key]->update([
                'is_root'            => $data['is_root'],
                'show_fallback_link' => $data['show_fallback_link'],
                'sort_order'         => $data['sort_order'],
            ]);
        }

        // -------------------------------------------------------------------------
        // 3. Options (liaisons)
        // -------------------------------------------------------------------------

        $optionsData = [
            // Q0 → niveau 1
            'q0' => [
                ['label' => 'J\'ai un problème ou une situation à améliorer, mais je ne sais pas encore quoi faire', 'next_question_key' => 'q1a', 'service_slug' => null],
                ['label' => 'J\'ai une idée ou un projet, je cherche comment le construire',                         'next_question_key' => 'q2a', 'service_slug' => null],
                ['label' => 'Mon projet existe, je cherche un appui pour avancer',                                   'next_question_key' => 'q3a', 'service_slug' => null],
            ],

            // Q1a → niveau 2 (Explorer)
            'q1a' => [
                ['label' => 'Seul·e ou en petit collectif',                                    'next_question_key' => 'q1b-solo', 'service_slug' => null],
                ['label' => 'Au sein d\'une organisation (entreprise, équipe, structure)',      'next_question_key' => 'q1b-org',  'service_slug' => null],
            ],

            // Q1b-solo → services
            'q1b-solo' => [
                ['label' => 'Tester une idée avec d\'autres avant de me lancer',       'next_question_key' => null, 'service_slug' => 'co-developpement'],
                ['label' => 'Comprendre un problème complexe avant d\'agir',           'next_question_key' => null, 'service_slug' => 'design-thinking'],
            ],

            // Q1b-org → services
            'q1b-org' => [
                ['label' => 'Faire travailler mon équipe sur un enjeu commun',                         'next_question_key' => null, 'service_slug' => 'seminaire-ic'],
                ['label' => 'Redéfinir la direction ou les priorités de ma structure',                 'next_question_key' => null, 'service_slug' => 'strategie'],
                ['label' => 'Impliquer des parties prenantes externes dans la réflexion',              'next_question_key' => null, 'service_slug' => 'co-design'],
            ],

            // Q2a → niveau 2 (Construire)
            'q2a' => [
                ['label' => 'Un projet à impact (social, territorial, associatif)', 'next_question_key' => 'q2b-impact', 'service_slug' => null],
                ['label' => 'Une activité économique ou entrepreneuriale',          'next_question_key' => 'q2b-eco',    'service_slug' => null],
            ],

            // Q2b-impact → services
            'q2b-impact' => [
                ['label' => 'Passer de l\'idée à un dossier solide (financement, partenaires)', 'next_question_key' => null, 'service_slug' => 'montage-projet'],
                ['label' => 'Concevoir le projet avec les personnes concernées',                'next_question_key' => null, 'service_slug' => 'co-design'],
                ['label' => 'Tester l\'idée en groupe avant de la formaliser',                 'next_question_key' => null, 'service_slug' => 'co-developpement'],
            ],

            // Q2b-eco → services
            'q2b-eco' => [
                ['label' => 'Créer ou développer mon activité',           'next_question_key' => null, 'service_slug' => 'entrepreneuriat'],
                ['label' => 'Définir ma stratégie de développement',      'next_question_key' => null, 'service_slug' => 'strategie'],
            ],

            // Q3a → niveau 2 (Accélérer)
            'q3a' => [
                ['label' => 'Le projet est lancé mais la mise en œuvre est difficile', 'next_question_key' => 'q3b-mise-en-oeuvre', 'service_slug' => null],
                ['label' => 'L\'équipe ou les parties prenantes ne sont pas alignées', 'next_question_key' => 'q3b-alignement',     'service_slug' => null],
                ['label' => 'Je veux revoir le cap ou pivoter',                        'next_question_key' => null,                 'service_slug' => 'strategie'],
                ['label' => 'Je cherche des financements ou partenariats',             'next_question_key' => null,                 'service_slug' => 'montage-projet'],
            ],

            // Q3b-mise-en-oeuvre → services
            'q3b-mise-en-oeuvre' => [
                ['label' => 'Je perds le fil, les jalons ne sont pas tenus',              'next_question_key' => null, 'service_slug' => 'gestion-projet'],
                ['label' => 'Mon modèle économique ou ma direction n\'est plus clair',    'next_question_key' => null, 'service_slug' => 'strategie'],
            ],

            // Q3b-alignement → services
            'q3b-alignement' => [
                ['label' => 'Créer un moment collectif pour (re)trouver une vision commune', 'next_question_key' => null, 'service_slug' => 'seminaire-ic'],
                ['label' => 'Débloquer un sujet avec du co-développement entre pairs',       'next_question_key' => null, 'service_slug' => 'co-developpement'],
            ],
        ];

        foreach ($optionsData as $questionKey => $options) {
            $question = $questionModels[$questionKey];

            foreach ($options as $i => $opt) {
                $nextQuestionId = $opt['next_question_key']
                    ? $questionModels[$opt['next_question_key']]->id
                    : null;

                $serviceId = $opt['service_slug']
                    ? ParcoursService::where('slug', $opt['service_slug'])->value('id')
                    : null;

                ParcoursOption::firstOrCreate(
                    [
                        'question_id' => $question->id,
                        'label'       => $opt['label'],
                    ],
                    [
                        'question_id'      => $question->id,
                        'label'            => $opt['label'],
                        'next_question_id' => $nextQuestionId,
                        'service_id'       => $serviceId,
                        'sort_order'       => $i,
                    ]
                );
            }
        }
    }
}
