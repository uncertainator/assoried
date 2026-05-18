<?php

namespace Database\Factories;

use App\Enums\ScrutinMajorityType;
use App\Enums\ScrutinQuorumType;
use App\Enums\ScrutinStatus;
use App\Models\Scrutin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Scrutin>
 */
class ScrutinFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'opened_at' => now()->subHour(),
            'closes_at' => now()->addDays(7),
            'quorum_type' => ScrutinQuorumType::Fixed,
            'quorum_value' => 5,
            'majority_type' => ScrutinMajorityType::Simple,
            'majority_threshold' => null,
            'status' => ScrutinStatus::Draft,
            'created_by' => User::factory()->state(['role' => 'admin']),
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => ScrutinStatus::Draft]);
    }

    public function open(): static
    {
        return $this->state([
            'status' => ScrutinStatus::Open,
            'opened_at' => now()->subHour(),
            'closes_at' => now()->addDays(7),
        ]);
    }

    public function closed(): static
    {
        return $this->state([
            'status' => ScrutinStatus::Closed,
            'opened_at' => now()->subDays(8),
            'closes_at' => now()->subDay(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => ScrutinStatus::Cancelled]);
    }

    public function proportional(float $percentage = 50.0): static
    {
        return $this->state([
            'quorum_type' => ScrutinQuorumType::Proportional,
            'quorum_value' => $percentage,
        ]);
    }

    public function qualified(float $threshold = 66.67): static
    {
        return $this->state([
            'majority_type' => ScrutinMajorityType::Qualified,
            'majority_threshold' => $threshold,
        ]);
    }
}
