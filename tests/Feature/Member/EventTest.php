<?php

namespace Tests\Feature\Member;

use App\Models\Circle;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Consultation — agenda cercle et agenda global
    // -----------------------------------------------------------------------

    public function test_membre_authentifie_consulte_agenda_cercle(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        $event = Event::factory()->create(['circle_id' => $circle->id]);

        $this->actingAs($adherent)
            ->get(route('member.circles.agenda', $circle))
            ->assertOk()
            ->assertSee($event->title);
    }

    public function test_membre_non_authentifie_est_redirige(): void
    {
        $circle = Circle::factory()->create();

        $this->get(route('member.circles.agenda', $circle))
            ->assertRedirect(route('login'));
    }

    public function test_membre_authentifie_consulte_agenda_global(): void
    {
        $adherent = User::factory()->adherent()->create();
        $event1 = Event::factory()->create();
        $event2 = Event::factory()->create();

        $this->actingAs($adherent)
            ->get(route('member.agenda.index'))
            ->assertOk()
            ->assertSee($event1->title)
            ->assertSee($event2->title);
    }

    // -----------------------------------------------------------------------
    // Création — référent de son cercle
    // -----------------------------------------------------------------------

    public function test_referent_cree_evenement_dans_son_cercle(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->post(route('member.agenda.store', $circle), [
                'title' => 'Réunion plénière',
                'starts_at' => '2026-06-20 19:00',
                'ends_at' => null,
                'description' => null,
                'location' => null,
            ])
            ->assertRedirect(route('member.circles.agenda', $circle))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('events', [
            'circle_id' => $circle->id,
            'title' => 'Réunion plénière',
        ]);
    }

    public function test_referent_voit_son_evenement_dans_agenda_global(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $event = Event::factory()->create(['circle_id' => $circle->id]);

        $adherent = User::factory()->adherent()->create();

        $this->actingAs($adherent)
            ->get(route('member.agenda.index'))
            ->assertOk()
            ->assertSee($event->title);
    }

    // -----------------------------------------------------------------------
    // Création — admin dans n'importe quel cercle
    // -----------------------------------------------------------------------

    public function test_admin_cree_evenement_dans_nimporte_quel_cercle(): void
    {
        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create(['referent_id' => null]);

        $this->actingAs($admin)
            ->post(route('member.agenda.store', $circle), [
                'title' => 'Assemblée générale',
                'starts_at' => '2026-09-01 18:00',
            ])
            ->assertRedirect(route('member.circles.agenda', $circle))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('events', [
            'circle_id' => $circle->id,
            'title' => 'Assemblée générale',
        ]);
    }

    // -----------------------------------------------------------------------
    // Création refusée — adhérent (403)
    // -----------------------------------------------------------------------

    public function test_adherent_recoit_403_sur_creation(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($adherent)
            ->get(route('member.agenda.create', $circle))
            ->assertForbidden();
    }

    public function test_adherent_recoit_403_sur_store(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($adherent)
            ->post(route('member.agenda.store', $circle), [
                'title' => 'Tentative',
                'starts_at' => '2026-06-01 10:00',
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Modification refusée — adhérent (403)
    // -----------------------------------------------------------------------

    public function test_adherent_recoit_403_sur_edition(): void
    {
        $adherent = User::factory()->adherent()->create();
        $event = Event::factory()->create();

        $this->actingAs($adherent)
            ->get(route('member.agenda.edit', $event))
            ->assertForbidden();
    }

    public function test_adherent_recoit_403_sur_update(): void
    {
        $adherent = User::factory()->adherent()->create();
        $event = Event::factory()->create();

        $this->actingAs($adherent)
            ->put(route('member.agenda.update', $event), [
                'title' => 'Hack',
                'starts_at' => '2026-07-01 10:00',
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Suppression refusée — adhérent (403)
    // -----------------------------------------------------------------------

    public function test_adherent_recoit_403_sur_suppression(): void
    {
        $adherent = User::factory()->adherent()->create();
        $event = Event::factory()->create();

        $this->actingAs($adherent)
            ->delete(route('member.agenda.destroy', $event))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Référent refusé sur cercle dont il n'est pas référent
    // -----------------------------------------------------------------------

    public function test_referent_recoit_403_sur_cercle_dont_il_nest_pas_referent(): void
    {
        $referent = User::factory()->referent()->create();
        $autreReferent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $autreReferent->id]);

        $this->actingAs($referent)
            ->post(route('member.agenda.store', $circle), [
                'title' => 'Tentative',
                'starts_at' => '2026-06-01 10:00',
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Validation des dates
    // -----------------------------------------------------------------------

    public function test_date_fin_anterieure_debut_retourne_422(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->post(route('member.agenda.store', $circle), [
                'title' => 'Mauvaises dates',
                'starts_at' => '2026-06-20 19:00',
                'ends_at' => '2026-06-20 18:00',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('ends_at');

        $this->assertDatabaseMissing('events', ['title' => 'Mauvaises dates']);
    }

    public function test_evenement_sans_date_fin_est_valide(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->post(route('member.agenda.store', $circle), [
                'title' => 'Ponctuel',
                'starts_at' => '2026-07-15 10:00',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('events', ['title' => 'Ponctuel', 'ends_at' => null]);
    }

    // -----------------------------------------------------------------------
    // Événements passés — toujours présents en base
    // -----------------------------------------------------------------------

    public function test_evenement_passe_reste_visible_en_base(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $past = Event::factory()->past()->create(['circle_id' => $circle->id]);

        $this->assertDatabaseHas('events', ['id' => $past->id]);

        $adherent = User::factory()->adherent()->create();
        $this->actingAs($adherent)
            ->get(route('member.circles.agenda', $circle))
            ->assertOk()
            ->assertSee($past->title);
    }

    // -----------------------------------------------------------------------
    // Suppression — admin dans n'importe quel cercle
    // -----------------------------------------------------------------------

    public function test_admin_supprime_evenement_dans_nimporte_quel_cercle(): void
    {
        $admin = User::factory()->admin()->create();
        $autreReferent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $autreReferent->id]);
        $event = Event::factory()->create(['circle_id' => $circle->id]);

        $this->actingAs($admin)
            ->delete(route('member.agenda.destroy', $event))
            ->assertRedirect(route('member.circles.agenda', $circle))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    // -----------------------------------------------------------------------
    // Suppression par référent
    // -----------------------------------------------------------------------

    public function test_suppression_par_referent_supprime_event(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $event = Event::factory()->create(['circle_id' => $circle->id]);

        $this->actingAs($referent)
            ->delete(route('member.agenda.destroy', $event))
            ->assertRedirect(route('member.circles.agenda', $circle))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }
}
