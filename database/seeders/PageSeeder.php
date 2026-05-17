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
                'content' => "Éditeur\n\nLa Fabrique — association citoyenne en cours de constitution.\nAlsace, France.\nContact : bonjour@lafabrique.fr\n\nHébergement\n\nCe site est hébergé par Planethoster.\n4416 Louis-B.-Mayer, Laval, Québec H7P 0G1, Canada.",
            ],
            [
                'slug' => 'confidentialite',
                'title' => 'Politique de confidentialité',
                'content' => "Données collectées\n\nLa Fabrique collecte uniquement les données nécessaires au fonctionnement de l'association : nom, adresse e-mail, et préférences de contact.\n\nUtilisation\n\nVos données ne sont jamais revendues ni transmises à des tiers.\n\nVos droits\n\nConformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression. Contactez-nous à bonjour@lafabrique.fr.",
            ],
            [
                'slug' => 'a-propos',
                'title' => 'À propos',
                'content' => "La Fabrique est une association citoyenne implantée en Alsace. Elle réunit des habitants engagés autour de cercles thématiques : mobilité, alimentation, numérique, et bien d'autres.\n\nNotre mission est de favoriser les initiatives locales et de renforcer les liens entre citoyens.",
            ],
        ];

        foreach ($pages as $data) {
            Page::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
