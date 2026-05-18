<?php

namespace Database\Factories;

use App\Enums\ConsultationMode;
use App\Enums\ConsultationSource;
use App\Models\Consultation;
use App\Models\ConsultationReponse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConsultationReponse>
 */
class ConsultationReponseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'consultation_id' => Consultation::factory(),
            'mode' => ConsultationMode::AvisLibre->value,
            'contenu' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'source' => ConsultationSource::Numerique->value,
            'masque' => false,
        ];
    }

    public function terrain(): static
    {
        return $this->state([
            'source' => ConsultationSource::Terrain->value,
            'ip_address' => null,
        ]);
    }

    public function masquee(): static
    {
        return $this->state(['masque' => true]);
    }
}
