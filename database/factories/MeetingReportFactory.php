<?php

namespace Database\Factories;

use App\Enums\MeetingReportStatus;
use App\Models\Meeting;
use App\Models\MeetingReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingReport>
 */
class MeetingReportFactory extends Factory
{
    protected $model = MeetingReport::class;

    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'created_by' => User::factory()->referent(),
            'status' => MeetingReportStatus::Draft,
            'participants' => fake()->sentence(),
            'agenda_notes' => [],
            'decisions' => [],
            'open_points' => [],
            'free_notes' => null,
            'published_at' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state([
            'status' => MeetingReportStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function published(): static
    {
        return $this->state([
            'status' => MeetingReportStatus::Published,
            'published_at' => now(),
        ]);
    }
}
