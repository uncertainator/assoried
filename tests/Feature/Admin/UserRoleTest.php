<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helpers enum
    // -----------------------------------------------------------------------

    public function test_is_admin_returns_true_for_admin(): void
    {
        $user = User::factory()->admin()->make();
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isReferent());
        $this->assertFalse($user->isAdherent());
    }

    public function test_is_referent_returns_true_for_referent(): void
    {
        $user = User::factory()->referent()->make();
        $this->assertTrue($user->isReferent());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isAdherent());
    }

    public function test_is_adherent_returns_true_for_adherent(): void
    {
        $user = User::factory()->adherent()->make();
        $this->assertTrue($user->isAdherent());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isReferent());
    }

    // -----------------------------------------------------------------------
    // Non-admin bloqué (403)
    // -----------------------------------------------------------------------

    public function test_adherent_cannot_access_promote_form(): void
    {
        $adherent = User::factory()->adherent()->create();
        $target = User::factory()->adherent()->create();

        $this->actingAs($adherent)
            ->get(route('admin.users.promote.form', $target))
            ->assertStatus(403);
    }

    public function test_adherent_cannot_submit_promotion(): void
    {
        $adherent = User::factory()->adherent()->create();
        $target = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($adherent)
            ->post(route('admin.users.promote', $target), ['circle_id' => $circle->id])
            ->assertStatus(403);
    }

    public function test_adherent_cannot_demote(): void
    {
        $adherent = User::factory()->adherent()->create();
        $referent = User::factory()->referent()->create();

        $this->actingAs($adherent)
            ->post(route('admin.users.demote', $referent))
            ->assertStatus(403);
    }

    // -----------------------------------------------------------------------
    // Promotion réussie
    // -----------------------------------------------------------------------

    public function test_admin_can_promote_adherent_to_referent(): void
    {
        $admin = User::factory()->admin()->create();
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create(['referent_id' => null]);

        $this->actingAs($admin)
            ->post(route('admin.users.promote', $adherent), ['circle_id' => $circle->id])
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame(UserRole::Referent, $adherent->fresh()->role);
        $this->assertSame($adherent->id, $circle->fresh()->referent_id);
    }

    // -----------------------------------------------------------------------
    // Cercle déjà pris
    // -----------------------------------------------------------------------

    public function test_promotion_fails_when_circle_already_has_referent(): void
    {
        $admin = User::factory()->admin()->create();
        $existingReferent = User::factory()->referent()->create();
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create(['referent_id' => $existingReferent->id]);

        $this->actingAs($admin)
            ->post(route('admin.users.promote', $adherent), ['circle_id' => $circle->id])
            ->assertSessionHasErrors(['circle_id' => 'Ce cercle a déjà un référent.']);

        $this->assertSame(UserRole::Adherent, $adherent->fresh()->role);
    }

    // -----------------------------------------------------------------------
    // Promotion d'un admin impossible
    // -----------------------------------------------------------------------

    public function test_promotion_fails_when_target_is_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $targetAdmin = User::factory()->admin()->create();
        $circle = Circle::factory()->create(['referent_id' => null]);

        $this->actingAs($admin)
            ->post(route('admin.users.promote', $targetAdmin), ['circle_id' => $circle->id])
            ->assertSessionHasErrors(['circle_id' => 'Un administrateur ne peut pas être promu référent.']);
    }

    // -----------------------------------------------------------------------
    // Rétrogradation
    // -----------------------------------------------------------------------

    public function test_admin_can_demote_referent_to_adherent(): void
    {
        $admin = User::factory()->admin()->create();
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($admin)
            ->post(route('admin.users.demote', $referent))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame(UserRole::Adherent, $referent->fresh()->role);
        $this->assertNull($circle->fresh()->referent_id);
    }
}
