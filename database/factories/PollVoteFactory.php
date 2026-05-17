<?php

namespace Database\Factories;

use App\Models\Poll;
use App\Models\PollVote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PollVote>
 */
class PollVoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'poll_id' => Poll::factory(),
            'user_id' => User::factory(),
            'choice' => 'oui',
        ];
    }
}
