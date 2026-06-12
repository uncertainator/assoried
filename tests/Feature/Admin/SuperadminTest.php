<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperadminTest extends TestCase
{
    use RefreshDatabase;

    // T9 — migration: a superadmin row can be persisted on sqlite (no CHECK block).
    public function test_superadmin_user_can_be_persisted(): void
    {
        $user = User::factory()->superadmin()->create();

        $this->assertSame(UserRole::Superadmin, $user->fresh()->role);
    }

    // T1 — superadmin inherits admin panel access (hierarchical isAdmin).
    public function test_superadmin_can_access_admin_routes(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    // Acceptance — superadmin can promote an adherent to admin; admin cannot (403).
    public function test_superadmin_can_promote_adherent_to_admin(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $target = User::factory()->adherent()->create();

        $this->actingAs($superadmin)
            ->post(route('admin.users.role', $target), ['role' => 'admin'])
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame(UserRole::Admin, $target->fresh()->role);
    }

    public function test_admin_cannot_promote_to_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->adherent()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.role', $target), ['role' => 'admin'])
            ->assertStatus(403);

        $this->assertSame(UserRole::Adherent, $target->fresh()->role);
    }

    // T17 — superadmin is never an offered role option, regardless of who is logged in.
    public function test_superadmin_option_absent_from_role_menu(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        User::factory()->adherent()->create();

        $html = $this->actingAs($superadmin)
            ->get(route('admin.users.index'))
            ->getContent();

        $this->assertStringNotContainsString('value="superadmin"', $html);
    }

    public function test_changing_role_to_superadmin_is_rejected(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $target = User::factory()->adherent()->create();

        $this->actingAs($superadmin)
            ->post(route('admin.users.role', $target), ['role' => 'superadmin'])
            ->assertSessionHasErrors('role');

        $this->assertSame(UserRole::Adherent, $target->fresh()->role);
    }

    // T13 — no inferior role (nor superadmin) may act on a superadmin target.
    public function test_admin_cannot_act_on_superadmin_target(): void
    {
        $admin = User::factory()->admin()->create();
        $superadminTarget = User::factory()->superadmin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.demote', $superadminTarget))
            ->assertStatus(403);

        $this->actingAs($admin)
            ->post(route('admin.users.role', $superadminTarget), ['role' => 'adherent'])
            ->assertStatus(403);

        $this->assertSame(UserRole::Superadmin, $superadminTarget->fresh()->role);
    }

    public function test_superadmin_cannot_demote_another_superadmin(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $other = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->post(route('admin.users.role', $other), ['role' => 'adherent'])
            ->assertStatus(403);

        $this->assertSame(UserRole::Superadmin, $other->fresh()->role);
    }

    // T3 — real impersonation: as adherent, an admin route returns 403.
    public function test_impersonating_adherent_blocks_admin_routes(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->withSession(['impersonate_role' => 'adherent'])
            ->get(route('admin.users.index'))
            ->assertStatus(403);
    }

    // T4 — impersonating admin keeps admin access.
    public function test_impersonating_admin_allows_admin_routes(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->withSession(['impersonate_role' => 'admin'])
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    // T5 — the simulated role NEVER persists to the database.
    public function test_impersonated_role_never_persists(): void
    {
        $superadmin = User::factory()->superadmin()->create([
            'password' => null,
            'password_setup_dismissed_at' => null,
        ]);

        $this->actingAs($superadmin)
            ->withSession(['impersonate_role' => 'adherent'])
            ->post(route('account.password.dismiss'), ['dont_show_again' => '1']);

        $this->assertSame(UserRole::Superadmin, $superadmin->fresh()->role);
    }

    // T6 — only a real superadmin may start impersonation; superadmin role refused.
    public function test_non_superadmin_cannot_start_impersonation(): void
    {
        foreach ([User::factory()->admin(), User::factory()->referent(), User::factory()->adherent()] as $factory) {
            $user = $factory->create();

            $this->actingAs($user)
                ->post(route('impersonate.start'), ['role' => 'adherent'])
                ->assertStatus(403);

            $this->assertNull(session('impersonate_role'));
        }
    }

    public function test_superadmin_cannot_impersonate_superadmin(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->post(route('impersonate.start'), ['role' => 'superadmin'])
            ->assertSessionHasErrors('role');
    }

    // T12 — starting impersonation logs the REAL superadmin id + endorsed role.
    public function test_start_impersonation_creates_audit_log(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->post(route('impersonate.start'), ['role' => 'adherent'])
            ->assertRedirect(route('member.dashboard')); // not back() to the admin panel (403)

        $this->assertSame('adherent', session('impersonate_role'));
        $this->assertDatabaseHas('audit_logs', [
            'type' => AuditLog::TYPE_IMPERSONATION_START,
            'actor_id' => $superadmin->id,
            'new_role' => 'adherent',
        ]);
    }

    // T7 — stop is reachable while impersonating adherent and restores superadmin.
    public function test_stop_impersonation_is_reachable_and_restores_role(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->withSession(['impersonate_role' => 'adherent'])
            ->post(route('impersonate.stop'))
            ->assertRedirect(route('admin.index'));

        $this->assertNull(session('impersonate_role'));
        $this->assertDatabaseHas('audit_logs', [
            'type' => AuditLog::TYPE_IMPERSONATION_STOP,
            'actor_id' => $superadmin->id,
        ]);

        // Admin access restored once the session is cleared.
        $this->actingAs($superadmin->fresh())
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    // T8 — a forged session for a non-superadmin is purged, no override applied.
    public function test_forged_impersonation_session_is_ignored(): void
    {
        $admin = User::factory()->admin()->create();

        // Admin keeps admin access (override not applied), and the route still
        // honors the admin role rather than the forged 'adherent'.
        $this->actingAs($admin)
            ->withSession(['impersonate_role' => 'adherent'])
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    // Acceptance — promotion writes a role_change audit entry.
    public function test_role_change_creates_audit_log(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $target = User::factory()->adherent()->create();

        $this->actingAs($superadmin)
            ->post(route('admin.users.role', $target), ['role' => 'admin']);

        $this->assertDatabaseHas('audit_logs', [
            'type' => AuditLog::TYPE_ROLE_CHANGE,
            'actor_id' => $superadmin->id,
            'target_user_id' => $target->id,
            'old_role' => 'adherent',
            'new_role' => 'admin',
        ]);
    }

    // T14 — audit viewer: real superadmin only; admin and impersonating superadmin 403.
    public function test_audit_log_viewer_access(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($superadmin)->get(route('audit-logs.index'))->assertOk();
        $this->actingAs($admin)->get(route('audit-logs.index'))->assertStatus(403);

        $this->actingAs($superadmin)
            ->withSession(['impersonate_role' => 'admin'])
            ->get(route('audit-logs.index'))
            ->assertStatus(403);
    }

    // T16 — tooling identity: a superadmin is NOT returned by the admin scope, so
    // existing admin notification fan-out / stats exclude it (no silent drift).
    public function test_superadmin_excluded_from_admin_scope(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $admin = User::factory()->admin()->create();

        $admins = User::admin()->pluck('id');

        $this->assertTrue($admins->contains($admin->id));
        $this->assertFalse($admins->contains($superadmin->id));
    }

    // T15 — the return banner is present while impersonating (both layouts share it).
    public function test_return_banner_present_during_impersonation(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $html = $this->actingAs($superadmin)
            ->withSession(['impersonate_role' => 'admin'])
            ->get(route('admin.users.index'))
            ->getContent();

        $this->assertStringContainsString(route('impersonate.stop'), $html);
        $this->assertStringContainsString('Revenir à superadmin', $html);
    }
}
