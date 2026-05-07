<?php

namespace Database\Factories;

use App\Models\Circle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Circle>
 */
class CircleFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'slug' => Str::slug($name),
            'name' => ucfirst($name),
            'description' => fake()->sentence(),
            'max_members' => null,
            'is_active' => true,
            'referent_id' => null,
        ];
    }
}
