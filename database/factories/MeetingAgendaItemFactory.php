<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingAgendaItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingAgendaItem>
 */
class MeetingAgendaItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'position' => 1,
            'title' => fake()->sentence(3),
            'duration_minutes' => fake()->optional()->randomElement([10, 15, 20, 30]),
        ];
    }
}
