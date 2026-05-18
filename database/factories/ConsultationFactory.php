<?php

namespace Database\Factories;

use App\Enums\ConsultationMode;
use App\Models\Consultation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consultation>
 */
class ConsultationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titre' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'date_cloture' => now()->addDays(7),
            'mode_recueil' => ConsultationMode::AvisLibre,
            'options' => null,
            'masque' => false,
        ];
    }

    public function ouverte(): static
    {
        return $this->state(['date_cloture' => now()->addDays(7)]);
    }

    public function cloturee(): static
    {
        return $this->state(['date_cloture' => now()->subDay()]);
    }

    public function voteIndicatif(): static
    {
        return $this->state([
            'mode_recueil' => ConsultationMode::VoteIndicatif,
            'options' => ['Option A', 'Option B', 'Option C'],
        ]);
    }

    public function signature(): static
    {
        return $this->state(['mode_recueil' => ConsultationMode::Signature]);
    }
}
