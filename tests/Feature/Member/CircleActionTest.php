<?php

namespace Tests\Feature\Member;

use App\Enums\CircleActionStatus;
use App\Models\Circle;
use App\Models\CircleAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CircleActionTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Création — référent de son cercle
    // -----------------------------------------------------------------------

    public function test_referent_cree_action_dans_son_cercle(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->post(route('member.circle.actions.store', $circle), [
                'title' => 'Organiser la réunion',
                'due_date' => '2026-06-30',
            ])
            ->assertRedirect(route('member.circles.show', $circle))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('circle_actions', [
            'circle_id' => $circle->id,
            'title' => 'Organiser la réunion',
            'status' => 'todo',
        ]);
    }

    // -----------------------------------------------------------------------
    // Création — admin dans n'importe quel cercle
    // -----------------------------------------------------------------------

    public function test_admin_cree_action_dans_nimporte_quel_cercle(): void
    {
        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create(['referent_id' => null]);

        $this->actingAs($admin)
            ->post(route('member.circle.actions.store', $circle), [
                'title' => 'Action admin',
                'due_date' => '2026-07-15',
            ])
            ->assertRedirect(route('member.circles.show', $circle))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('circle_actions', [
            'circle_id' => $circle->id,
            'title' => 'Action admin',
        ]);
    }

    // -----------------------------------------------------------------------
    // Création refusée — adhérent (403)
    // -----------------------------------------------------------------------

    public function test_adherent_recoit_403_sur_creation_action(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($adherent)
            ->post(route('member.circle.actions.store', $circle), [
                'title' => 'Tentative',
                'due_date' => '2026-06-30',
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Création refusée — référent d'un autre cercle (403)
    // -----------------------------------------------------------------------

    public function test_referent_recoit_403_sur_cercle_dont_il_nest_pas_referent(): void
    {
        $referent = User::factory()->referent()->create();
        $autreReferent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $autreReferent->id]);

        $this->actingAs($referent)
            ->post(route('member.circle.actions.store', $circle), [
                'title' => 'Tentative',
                'due_date' => '2026-06-30',
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Mise à jour du statut — référent de son cercle
    // -----------------------------------------------------------------------

    public function test_referent_met_a_jour_statut_action(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $action = CircleAction::factory()->create([
            'circle_id' => $circle->id,
            'status' => CircleActionStatus::Todo,
        ]);

        $this->actingAs($referent)
            ->patch(route('member.circle.actions.update', $action), [
                'status' => 'in_progress',
            ])
            ->assertRedirect(route('member.circles.show', $circle))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('circle_actions', [
            'id' => $action->id,
            'status' => 'in_progress',
        ]);
    }

    // -----------------------------------------------------------------------
    // Mise à jour du statut — adhérent (403)
    // -----------------------------------------------------------------------

    public function test_adherent_recoit_403_sur_update_statut(): void
    {
        $adherent = User::factory()->adherent()->create();
        $action = CircleAction::factory()->create();

        $this->actingAs($adherent)
            ->patch(route('member.circle.actions.update', $action), [
                'status' => 'done',
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Suppression — référent de son cercle
    // -----------------------------------------------------------------------

    public function test_referent_supprime_action_de_son_cercle(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $action = CircleAction::factory()->create(['circle_id' => $circle->id]);

        $this->actingAs($referent)
            ->delete(route('member.circle.actions.destroy', $action))
            ->assertRedirect(route('member.circles.show', $circle))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('circle_actions', ['id' => $action->id]);
    }

    // -----------------------------------------------------------------------
    // Suppression — admin dans n'importe quel cercle
    // -----------------------------------------------------------------------

    public function test_admin_supprime_action_dans_nimporte_quel_cercle(): void
    {
        $admin = User::factory()->admin()->create();
        $action = CircleAction::factory()->create();

        $this->actingAs($admin)
            ->delete(route('member.circle.actions.destroy', $action))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('circle_actions', ['id' => $action->id]);
    }

    // -----------------------------------------------------------------------
    // Validation — titre manquant
    // -----------------------------------------------------------------------

    public function test_creation_action_sans_titre_retourne_erreur(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->post(route('member.circle.actions.store', $circle), [
                'due_date' => '2026-06-30',
            ])
            ->assertSessionHasErrors('title');

        $this->assertDatabaseCount('circle_actions', 0);
    }
}
