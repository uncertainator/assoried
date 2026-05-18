<?php

namespace Database\Factories;

use App\Models\Scrutin;
use App\Models\ScrutinOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScrutinOption>
 */
class ScrutinOptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'scrutin_id' => Scrutin::factory(),
            'label' => fake()->words(3, true),
            'position' => 1,
        ];
    }
}
