<?php

namespace Database\Factories;

use App\Enums\PollType;
use App\Models\Poll;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Poll>
 */
class PollFactory extends Factory
{
    public function definition(): array
    {
        return [
            'circle_id' => null,
            'created_by' => User::factory(),
            'title' => fake()->sentence(6),
            'type' => PollType::YesNo,
            'options' => null,
            'closes_at' => now()->addDays(7),
        ];
    }

    public function open(): static
    {
        return $this->state(['closes_at' => now()->addDays(7)]);
    }

    public function closed(): static
    {
        return $this->state(['closes_at' => now()->subDay()]);
    }

    public function yesNo(): static
    {
        return $this->state(['type' => PollType::YesNo, 'options' => null]);
    }

    public function multiple(): static
    {
        return $this->state([
            'type' => PollType::Multiple,
            'options' => ['Option A', 'Option B', 'Option C'],
        ]);
    }
}
