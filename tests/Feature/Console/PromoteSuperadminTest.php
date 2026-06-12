<?php

namespace Tests\Feature\Console;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoteSuperadminTest extends TestCase
{
    use RefreshDatabase;

    // T10 — existing user is promoted to superadmin.
    public function test_promotes_existing_user(): void
    {
        $user = User::factory()->admin()->create(['email' => 'admin@lafabrique.fr']);

        $this->artisan('superadmin:promote', ['email' => 'admin@lafabrique.fr'])
            ->assertExitCode(0);

        $this->assertSame(UserRole::Superadmin, $user->fresh()->role);
    }

    // T10 — missing email fails cleanly with no DB change.
    public function test_fails_on_missing_email(): void
    {
        $this->artisan('superadmin:promote', ['email' => 'nobody@example.com'])
            ->assertExitCode(1);

        $this->assertDatabaseMissing('users', ['role' => 'superadmin']);
    }

    // T10 — idempotent: rerun does not break or change anything.
    public function test_is_idempotent(): void
    {
        $user = User::factory()->superadmin()->create(['email' => 'admin@lafabrique.fr']);

        $this->artisan('superadmin:promote', ['email' => 'admin@lafabrique.fr'])
            ->assertExitCode(0);

        $this->assertSame(UserRole::Superadmin, $user->fresh()->role);
        $this->assertSame(1, User::where('role', UserRole::Superadmin)->count());
    }
}
