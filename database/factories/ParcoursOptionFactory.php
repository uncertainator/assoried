<?php

namespace Database\Factories;

use App\Models\ParcoursOption;
use App\Models\ParcoursQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ParcoursOption>
 */
class ParcoursOptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question_id' => ParcoursQuestion::factory(),
            'label' => $this->faker->sentence(3),
            'next_question_id' => null,
            'service_id' => null,
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }

    public function toQuestion(int $questionId): static
    {
        return $this->state(['next_question_id' => $questionId, 'service_id' => null]);
    }

    public function toService(int $serviceId): static
    {
        return $this->state(['service_id' => $serviceId, 'next_question_id' => null]);
    }

    public function unconfigured(): static
    {
        return $this->state(['next_question_id' => null, 'service_id' => null]);
    }
}
