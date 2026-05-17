<?php

namespace Database\Factories;

use App\Models\ParcoursQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ParcoursQuestion>
 */
class ParcoursQuestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'label' => $this->faker->sentence(6).'?',
            'is_root' => false,
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }

    public function root(): static
    {
        return $this->state(['is_root' => true]);
    }
}
