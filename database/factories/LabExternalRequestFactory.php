<?php

namespace Database\Factories;

use App\Enums\LabRequestStatus;
use App\Models\LabExternalRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabExternalRequest>
 */
class LabExternalRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['citoyen', 'entreprise']),
            'nom_contact' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'telephone' => null,
            'raison_sociale' => null,
            'territoire' => null,
            'besoin_type' => null,
            'message' => $this->faker->paragraph(),
            'statut' => LabRequestStatus::Nouvelle,
            'rgpd_consent' => true,
        ];
    }

    public function citoyen(): static
    {
        return $this->state([
            'type' => 'citoyen',
            'territoire' => $this->faker->city(),
        ]);
    }

    public function entreprise(): static
    {
        return $this->state([
            'type' => 'entreprise',
            'raison_sociale' => $this->faker->company(),
            'telephone' => $this->faker->phoneNumber(),
            'besoin_type' => $this->faker->randomElement(['facilitation', 'innovation', 'autre']),
        ]);
    }

    public function enCours(): static
    {
        return $this->state(['statut' => LabRequestStatus::EnCours]);
    }

    public function traitee(): static
    {
        return $this->state(['statut' => LabRequestStatus::Traitee]);
    }
}
