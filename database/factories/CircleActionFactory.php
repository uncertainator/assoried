<?php

namespace Database\Factories;

use App\Enums\CircleActionStatus;
use App\Models\Circle;
use App\Models\CircleAction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CircleAction>
 */
class CircleActionFactory extends Factory
{
    protected $model = CircleAction::class;

    public function definition(): array
    {
        return [
            'circle_id' => Circle::factory(),
            'author_id' => User::factory()->referent(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'due_date' => $this->faker->dateTimeBetween('+1 day', '+60 days')->format('Y-m-d'),
            'status' => CircleActionStatus::Todo,
        ];
    }

    public function done(): static
    {
        return $this->state(['status' => CircleActionStatus::Done]);
    }

    public function inProgress(): static
    {
        return $this->state(['status' => CircleActionStatus::InProgress]);
    }
}
