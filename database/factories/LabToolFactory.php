<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LabToolFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['Design Thinking', 'Facilitation', 'Idéation']),
            'file_path' => 'fake-tool.pdf',
            'downloads_count' => 0,
            'active' => true,
            'created_by' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['active' => false]);
    }
}
