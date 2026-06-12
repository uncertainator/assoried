<?php

namespace Tests\Feature\Member;

use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CircleSplitTest extends TestCase
{
    use RefreshDatabase;

    // ----------------------------------------------------------------
    // "Cercles à rejoindre" : exclut approved + pending, garde rejected + vierge
    // ----------------------------------------------------------------
    public function test_discover_excludes_approved_and_pending_circles(): void
    {
        $adherent = User::factory()->adherent()->create();

        $approved = Circle::factory()->create(['name' => 'Cercle Approuvé']);
        $pending = Circle::factory()->create(['name' => 'Cercle En Attente']);
        $rejected = Circle::factory()->create(['name' => 'Cercle Refusé']);
        $vierge = Circle::factory()->create(['name' => 'Cercle Vierge']);

        CircleMembership::factory()->approved()->create(['user_id' => $adherent->id, 'circle_id' => $approved->id]);
        CircleMembership::factory()->pending()->create(['user_id' => $adherent->id, 'circle_id' => $pending->id]);
        CircleMembership::factory()->rejected()->create(['user_id' => $adherent->id, 'circle_id' => $rejected->id]);

        $response = $this->actingAs($adherent)->get(route('member.circles.discover'));

        $response->assertOk();
        $response->assertSee('Cercle Refusé');
        $response->assertSee('Cercle Vierge');
        $response->assertDontSee('Cercle Approuvé');
        $response->assertDontSee('Cercle En Attente');
    }

    // ----------------------------------------------------------------
    // "Mes cercles" : approved + pending avec libellés distincts, pas les vierges
    // ----------------------------------------------------------------
    public function test_index_shows_approved_and_pending_with_distinct_labels(): void
    {
        $adherent = User::factory()->adherent()->create();

        $approved = Circle::factory()->create(['name' => 'Cercle Approuvé']);
        $pending = Circle::factory()->create(['name' => 'Cercle En Attente']);
        $vierge = Circle::factory()->create(['name' => 'Cercle Vierge']);

        CircleMembership::factory()->approved()->create(['user_id' => $adherent->id, 'circle_id' => $approved->id]);
        CircleMembership::factory()->pending()->create(['user_id' => $adherent->id, 'circle_id' => $pending->id]);

        $response = $this->actingAs($adherent)->get(route('member.circles.index'));

        $response->assertOk();
        $response->assertSee('Cercle Approuvé');
        $response->assertSee('Cercle En Attente');
        $response->assertDontSee('Cercle Vierge');
        // Libellés distinguant rejoint vs en attente
        $response->assertSee('Membre');
        $response->assertSee('En attente de réponse');
    }

    // ----------------------------------------------------------------
    // Scope par membre : les adhésions d'un autre user ne fuient pas
    // ----------------------------------------------------------------
    public function test_circles_are_scoped_to_authenticated_member(): void
    {
        $alice = User::factory()->adherent()->create();
        $bob = User::factory()->adherent()->create();

        $bobCircle = Circle::factory()->create(['name' => 'Cercle De Bob']);
        CircleMembership::factory()->approved()->create(['user_id' => $bob->id, 'circle_id' => $bobCircle->id]);

        // Mes cercles d'Alice : ne doit PAS voir le cercle de Bob
        $this->actingAs($alice)->get(route('member.circles.index'))
            ->assertOk()
            ->assertDontSee('Cercle De Bob');

        // Discover d'Alice : le cercle de Bob reste joignable pour Alice (elle n'y a aucune adhésion)
        $this->actingAs($alice)->get(route('member.circles.discover'))
            ->assertOk()
            ->assertSee('Cercle De Bob');
    }

    // ----------------------------------------------------------------
    // Non-chevauchement : un cercle n'est jamais sur les deux pages
    // ----------------------------------------------------------------
    public function test_no_circle_appears_on_both_pages(): void
    {
        $adherent = User::factory()->adherent()->create();

        $joined = Circle::factory()->create(['name' => 'Cercle Rejoint']);
        $open = Circle::factory()->create(['name' => 'Cercle Ouvert']);

        CircleMembership::factory()->approved()->create(['user_id' => $adherent->id, 'circle_id' => $joined->id]);

        $index = $this->actingAs($adherent)->get(route('member.circles.index'));
        $index->assertSee('Cercle Rejoint');
        $index->assertDontSee('Cercle Ouvert');

        $discover = $this->actingAs($adherent)->get(route('member.circles.discover'));
        $discover->assertSee('Cercle Ouvert');
        $discover->assertDontSee('Cercle Rejoint');
    }

    // ----------------------------------------------------------------
    // Action "rejoindre" : bascule le cercle de discover vers mes cercles (pending)
    // ----------------------------------------------------------------
    public function test_joining_from_discover_moves_circle_to_index_as_pending(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create(['name' => 'Cercle À Rejoindre']);

        // Avant : visible dans discover, absent de mes cercles
        $this->actingAs($adherent)->get(route('member.circles.discover'))->assertSee('Cercle À Rejoindre');
        $this->actingAs($adherent)->get(route('member.circles.index'))->assertDontSee('Cercle À Rejoindre');

        $this->actingAs($adherent)->post(route('member.circles.join', $circle))->assertRedirect();

        $this->assertDatabaseHas('circle_user', [
            'user_id' => $adherent->id,
            'circle_id' => $circle->id,
            'status' => 'pending',
        ]);

        // Après : apparaît dans mes cercles en attente.
        // (Ce GET consomme aussi le flash de succès qui répète le nom du cercle.)
        $this->actingAs($adherent)->get(route('member.circles.index'))
            ->assertSee('Cercle À Rejoindre')
            ->assertSee('En attente de réponse');

        // …et quitte la page "à rejoindre".
        $this->actingAs($adherent)->get(route('member.circles.discover'))->assertDontSee('Cercle À Rejoindre');
    }
}
