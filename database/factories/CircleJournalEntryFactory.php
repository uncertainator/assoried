<?php

namespace Database\Factories;

use App\Models\Circle;
use App\Models\CircleJournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CircleJournalEntry>
 */
class CircleJournalEntryFactory extends Factory
{
    protected $model = CircleJournalEntry::class;

    public function definition(): array
    {
        return [
            'circle_id' => Circle::factory(),
            'created_by' => User::factory()->referent(),
            'title' => fake()->sentence(5),
            'content' => fake()->paragraphs(3, true),
            'entry_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        ];
    }

    public function past(): static
    {
        return $this->state(['entry_date' => now()->subDays(30)->format('Y-m-d')]);
    }

    public function today(): static
    {
        return $this->state(['entry_date' => today()->format('Y-m-d')]);
    }
}
