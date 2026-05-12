<?php

namespace Database\Factories;

use App\Enums\MembershipStatus;
use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CircleMembership>
 */
class CircleMembershipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'circle_id' => Circle::factory(),
            'status' => MembershipStatus::Pending,
            'joined_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => MembershipStatus::Pending]);
    }

    public function approved(): static
    {
        return $this->state([
            'status' => MembershipStatus::Approved,
            'validated_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => MembershipStatus::Rejected,
            'validated_at' => now(),
            'rejection_reason' => fake()->sentence(),
        ]);
    }
}
