<?php

namespace Database\Factories;

use App\Models\Circle;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'circle_id' => Circle::factory(),
            'author_id' => User::factory()->referent(),
            'body' => fake()->paragraphs(2, true),
            'pushed_to_general' => false,
            'pushed_at' => null,
        ];
    }

    public function pushed(): static
    {
        return $this->state([
            'pushed_to_general' => true,
            'pushed_at' => now(),
        ]);
    }
}
