<?php

namespace Database\Factories;

use App\Models\Scrutin;
use App\Models\ScrutinOption;
use App\Models\ScrutinVote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScrutinVote>
 */
class ScrutinVoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'scrutin_id' => Scrutin::factory(),
            'scrutin_option_id' => ScrutinOption::factory(),
            'user_id' => User::factory(),
        ];
    }
}
