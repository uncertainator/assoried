<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRole::Adherent,
        ];
    }

    public function admin(): static
    {
        return $this->state(['role' => UserRole::Admin]);
    }

    public function referent(): static
    {
        return $this->state(['role' => UserRole::Referent]);
    }

    public function adherent(): static
    {
        return $this->state(['role' => UserRole::Adherent]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function magicLinkOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => null,
            'password_setup_dismissed_at' => null,
        ]);
    }

    public function withDismissedSetup(): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => null,
            'password_setup_dismissed_at' => now(),
        ]);
    }
}
