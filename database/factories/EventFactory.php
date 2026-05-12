<?php

namespace Database\Factories;

use App\Models\Circle;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('now', '+6 months');

        return [
            'circle_id' => Circle::factory(),
            'author_id' => User::factory()->referent(),
            'title' => fake()->sentence(4),
            'starts_at' => $start,
            'ends_at' => null,
            'description' => fake()->optional()->sentence(),
            'location' => fake()->optional()->city(),
        ];
    }

    public function past(): static
    {
        return $this->state(['starts_at' => now()->subDays(3)]);
    }

    public function withEnd(): static
    {
        return $this->state(fn (array $attributes) => [
            'ends_at' => Carbon::parse($attributes['starts_at'])->addHours(2),
        ]);
    }
}
