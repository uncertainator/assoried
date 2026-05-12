<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAgendaTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_accessible_sans_authentification(): void
    {
        $this->get(route('public.agenda'))
            ->assertOk();
    }

    public function test_evenement_public_futur_apparait(): void
    {
        $event = Event::factory()->public()->create();

        $this->get(route('public.agenda'))
            ->assertOk()
            ->assertSee($event->title);
    }

    public function test_evenement_non_public_absent(): void
    {
        $event = Event::factory()->create(['is_public' => false]);

        $this->get(route('public.agenda'))
            ->assertOk()
            ->assertDontSee($event->title);
    }

    public function test_evenement_passe_absent(): void
    {
        $event = Event::factory()->public()->past()->create();

        $this->get(route('public.agenda'))
            ->assertOk()
            ->assertDontSee($event->title);
    }

    public function test_description_absente_du_html(): void
    {
        $event = Event::factory()->public()->create([
            'description' => 'Information interne confidentielle',
        ]);

        $this->get(route('public.agenda'))
            ->assertOk()
            ->assertSee($event->title)
            ->assertDontSee($event->description);
    }

    public function test_evenement_repasse_prive_disparait(): void
    {
        $event = Event::factory()->public()->create();

        $event->update(['is_public' => false]);

        $this->get(route('public.agenda'))
            ->assertOk()
            ->assertDontSee($event->title);
    }

    public function test_lieu_affiche_si_renseigne(): void
    {
        $event = Event::factory()->public()->create(['location' => 'Salle polyvalente']);

        $this->get(route('public.agenda'))
            ->assertOk()
            ->assertSee('Salle polyvalente');
    }

    public function test_cercle_organisateur_affiche(): void
    {
        $event = Event::factory()->public()->create();

        $this->get(route('public.agenda'))
            ->assertOk()
            ->assertSee($event->circle->name, false);
    }
}
