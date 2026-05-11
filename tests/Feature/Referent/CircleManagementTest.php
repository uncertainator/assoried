<?php

namespace Tests\Feature\Referent;

use App\Models\Circle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CircleManagementTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Circle::isManagedBy()
    // -----------------------------------------------------------------------

    public function test_is_managed_by_returns_true_for_admin(): void
    {
        $admin = User::factory()->admin()->make();
        $circle = Circle::factory()->make(['referent_id' => null]);

        $this->assertTrue($circle->isManagedBy($admin));
    }

    public function test_is_managed_by_returns_true_for_assigned_referent(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->assertTrue($circle->isManagedBy($referent));
    }

    public function test_is_managed_by_returns_false_for_other_referent(): void
    {
        $referent = User::factory()->referent()->create();
        $otherReferent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->assertFalse($circle->isManagedBy($otherReferent));
    }

    public function test_is_managed_by_returns_false_for_adherent(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create(['referent_id' => null]);

        $this->assertFalse($circle->isManagedBy($adherent));
    }

    // -----------------------------------------------------------------------
    // Accès à l'espace référent — middleware isReferent || isAdmin
    // -----------------------------------------------------------------------

    public function test_adherent_cannot_access_referent_circle_edit(): void
    {
        $adherent = User::factory()->adherent()->create();

        $this->actingAs($adherent)
            ->get(route('referent.circle.edit'))
            ->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_referent_circle_edit(): void
    {
        $this->get(route('referent.circle.edit'))
            ->assertRedirect(route('login'));
    }

    // -----------------------------------------------------------------------
    // Référent sans cercle assigné → redirection
    // -----------------------------------------------------------------------

    public function test_referent_without_circle_is_redirected_on_edit(): void
    {
        $referent = User::factory()->referent()->create();

        $this->actingAs($referent)
            ->get(route('referent.circle.edit'))
            ->assertRedirect(route('member.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_referent_without_circle_is_redirected_on_update(): void
    {
        $referent = User::factory()->referent()->create();

        $this->actingAs($referent)
            ->put(route('referent.circle.update'), ['name' => 'Nouveau nom'])
            ->assertRedirect(route('member.dashboard'))
            ->assertSessionHas('error');
    }

    // -----------------------------------------------------------------------
    // Référent autorisé — édition réussie
    // -----------------------------------------------------------------------

    public function test_referent_can_view_their_circle_edit_form(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->get(route('referent.circle.edit'))
            ->assertOk()
            ->assertViewIs('referent.circle.edit')
            ->assertViewHas('circle', $circle);
    }

    public function test_referent_can_update_their_circle(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->put(route('referent.circle.update'), [
                'name' => 'Nouveau nom du cercle',
                'description' => 'Une nouvelle description.',
            ])
            ->assertRedirect(route('referent.circle.edit'))
            ->assertSessionHas('success');

        $this->assertSame('Nouveau nom du cercle', $circle->fresh()->name);
        $this->assertSame('Une nouvelle description.', $circle->fresh()->description);
    }

    public function test_referent_cannot_update_slug(): void
    {
        $referent = User::factory()->referent()->create();
        $originalSlug = 'mobilite';
        $circle = Circle::factory()->create(['referent_id' => $referent->id, 'slug' => $originalSlug]);

        $this->actingAs($referent)
            ->put(route('referent.circle.update'), [
                'name' => 'Mobilité Modifié',
                'description' => null,
            ])
            ->assertRedirect(route('referent.circle.edit'));

        $this->assertSame($originalSlug, $circle->fresh()->slug);
    }

    public function test_update_requires_name(): void
    {
        $referent = User::factory()->referent()->create();
        Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->put(route('referent.circle.update'), ['name' => '', 'description' => 'Test'])
            ->assertSessionHasErrors('name');
    }

    // -----------------------------------------------------------------------
    // Admin peut accéder à l'espace référent (middleware)
    // -----------------------------------------------------------------------

    public function test_admin_can_access_referent_area_but_redirected_without_circle(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('referent.circle.edit'))
            ->assertRedirect(route('member.dashboard'))
            ->assertSessionHas('error');
    }

    // -----------------------------------------------------------------------
    // CirclePolicy — update
    // -----------------------------------------------------------------------

    public function test_circle_policy_allows_update_for_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create(['referent_id' => null]);

        $this->assertTrue($admin->can('update', $circle));
    }

    public function test_circle_policy_allows_update_for_assigned_referent(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->assertTrue($referent->can('update', $circle));
    }

    public function test_circle_policy_denies_update_for_other_referent(): void
    {
        $referent = User::factory()->referent()->create();
        $otherReferent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->assertFalse($otherReferent->can('update', $circle));
    }

    public function test_circle_policy_denies_update_for_adherent(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create(['referent_id' => null]);

        $this->assertFalse($adherent->can('update', $circle));
    }
}
