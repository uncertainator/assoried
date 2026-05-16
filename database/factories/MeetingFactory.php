<?php

namespace Database\Factories;

use App\Models\Circle;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meeting>
 */
class MeetingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'circle_id' => Circle::factory(),
            'created_by' => User::factory()->referent(),
            'title' => fake()->sentence(4),
            'scheduled_at' => fake()->dateTimeBetween('now', '+6 months'),
            'duration_minutes' => fake()->optional()->randomElement([30, 60, 90, 120]),
            'location' => fake()->optional()->city(),
            'visio_url' => null,
        ];
    }

    public function past(): static
    {
        return $this->state(['scheduled_at' => now()->subDays(3)]);
    }
}
