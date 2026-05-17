<?php

namespace Database\Factories;

use App\Enums\ParcoursCtaType;
use App\Models\ParcoursService;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ParcoursService>
 */
class ParcoursServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Co-développement',
                'Design Thinking',
                'Co-design',
                'Montage de projet',
                'Gestion de projet',
                'Entrepreneuriat',
                'Séminaire Intelligence Collective',
                'Stratégie',
            ]),
            'description' => $this->faker->paragraph(),
            'use_cases' => $this->faker->paragraph(),
            'cta_type' => $this->faker->randomElement(ParcoursCtaType::cases())->value,
            'cta_value' => $this->faker->url(),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 10),
            'created_by' => User::factory(),
        ];
    }
}
