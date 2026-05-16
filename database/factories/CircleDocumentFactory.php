<?php

namespace Database\Factories;

use App\Enums\CircleDocumentType;
use App\Models\Circle;
use App\Models\CircleDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CircleDocument>
 */
class CircleDocumentFactory extends Factory
{
    protected $model = CircleDocument::class;

    public function definition(): array
    {
        return [
            'circle_id' => Circle::factory(),
            'uploaded_by' => User::factory()->referent(),
            'title' => ucfirst(fake()->words(4, true)),
            'type' => CircleDocumentType::Pdf,
            'document_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'tags' => fake()->randomElements(
                ['statuts', 'réunion', 'finances', 'rapport', 'compte-rendu'],
                fake()->numberBetween(0, 3)
            ),
            'description' => fake()->optional()->paragraph(),
            'url' => null,
            'file_path' => 'circle-documents/1/'.Str::uuid().'.pdf',
            'original_filename' => fake()->word().'.pdf',
        ];
    }

    public function asLink(): static
    {
        return $this->state([
            'type' => CircleDocumentType::Link,
            'url' => fake()->url(),
            'file_path' => null,
            'original_filename' => null,
        ]);
    }

    public function asPdf(): static
    {
        return $this->state([
            'type' => CircleDocumentType::Pdf,
            'url' => null,
        ]);
    }

    public function withTags(array $tags): static
    {
        return $this->state(['tags' => $tags]);
    }

    public function forCircle(Circle $circle): static
    {
        return $this->state(['circle_id' => $circle->id]);
    }
}
