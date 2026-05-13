<?php

namespace Database\Factories;

use App\Models\LabService;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabService>
 */
class LabServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'category' => $this->faker->randomElement([
                'Facilitation',
                'Gestion de projet',
                'Innovation',
                'Communication',
                'Autre',
            ]),
            'description' => $this->faker->paragraph(),
            'created_by' => User::factory(),
        ];
    }
}
