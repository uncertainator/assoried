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
                'content' => "<h2>Données collectées</h2><p>La Fabrique collecte uniquement les données nécessaires au fonctionnement de l'association : nom, adresse e-mail, et préférences de contact.</p><h2>Utilisation</h2><p>Vos données ne sont jamais revendues ni transmises à des tiers.</p><h2>Vos droits</h2><p>Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression. Contactez-nous à bonjour@lafabrique.fr.</p>",
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
