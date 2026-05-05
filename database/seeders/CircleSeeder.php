<?php

namespace Database\Seeders;

use App\Models\Circle;
use Illuminate\Database\Seeder;

class CircleSeeder extends Seeder
{
    public function run(): void
    {
        $circles = [
            [
                'slug'        => 'mobilite',
                'name'        => 'Mobilité & transports',
                'description' => 'Covoiturage, mobilités douces, réduction de l\'impact des déplacements au quotidien.',
                'is_active'   => true,
            ],
            [
                'slug'        => 'intergenerationnel',
                'name'        => 'Intergénérationnel',
                'description' => 'Lien entre les générations, entraide, transmission de savoirs, solidarité de voisinage.',
                'is_active'   => true,
            ],
            [
                'slug'        => 'finance',
                'name'        => 'Finances & gouvernance',
                'description' => 'Gestion des ressources de l\'association, transparence, budget participatif.',
                'is_active'   => true,
            ],
            [
                'slug'        => 'pilotage',
                'name'        => 'Pilotage & stratégie',
                'description' => 'Orientation de l\'association, projets à long terme, partenariats.',
                'is_active'   => true,
            ],
            [
                'slug'        => 'lab',
                'name'        => 'Fabrique à projets',
                'description' => 'Incubateur d\'idées : vous avez un projet, on vous aide à le mettre en route.',
                'is_active'   => true,
            ],
            [
                'slug'        => 'communication',
                'name'        => 'Communication',
                'description' => 'Visibilité de l\'association, réseaux sociaux, newsletter, affiches et événements.',
                'is_active'   => true,
            ],
        ];

        foreach ($circles as $data) {
            Circle::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
