<?php

namespace Database\Factories;

use App\Enums\LabRequestStatus;
use App\Models\Circle;
use App\Models\LabInternalRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabInternalRequest>
 */
class LabInternalRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'circle_id' => Circle::factory(),
            'user_id' => User::factory(),
            'lab_service_id' => null,
            'message' => $this->faker->paragraph(),
            'desired_date' => $this->faker->optional()->dateTimeBetween('now', '+6 months')?->format('Y-m-d'),
            'status' => LabRequestStatus::Nouvelle,
        ];
    }

    public function enCours(): static
    {
        return $this->state(['status' => LabRequestStatus::EnCours]);
    }

    public function traitee(): static
    {
        return $this->state(['status' => LabRequestStatus::Traitee]);
    }
}
