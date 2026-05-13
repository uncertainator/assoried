<?php

namespace Tests\Feature;

use App\Models\Circle;
use App\Models\LabService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabServiceTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Lecture : tout membre connecté
    // -------------------------------------------------------

    public function test_authenticated_member_can_view_catalogue(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->get('/lab/services')
            ->assertOk();
    }

    public function test_authenticated_member_can_view_service(): void
    {
        $member = User::factory()->create();
        $service = LabService::factory()->create();

        $this->actingAs($member)
            ->get("/lab/services/{$service->id}")
            ->assertOk()
            ->assertSee($service->title);
    }

    public function test_guest_is_redirected_from_catalogue(): void
    {
        $this->get('/lab/services')->assertRedirect('/connexion');
    }

    // -------------------------------------------------------
    // Create : accès refusé à un adhérent standard
    // -------------------------------------------------------

    public function test_adherent_cannot_access_create(): void
    {
        $adherent = User::factory()->create();

        $this->actingAs($adherent)
            ->get('/lab/services/create')
            ->assertForbidden();
    }

    // -------------------------------------------------------
    // Create : autorisé pour admin
    // -------------------------------------------------------

    public function test_admin_can_access_create(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/lab/services/create')
            ->assertOk();
    }

    // -------------------------------------------------------
    // Create : autorisé pour le référent du cercle Lab
    // -------------------------------------------------------

    public function test_lab_referent_can_access_create(): void
    {
        $referent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'lab', 'referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->get('/lab/services/create')
            ->assertOk();
    }

    public function test_other_referent_cannot_access_create(): void
    {
        $referent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'autre-cercle', 'referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->get('/lab/services/create')
            ->assertForbidden();
    }

    // -------------------------------------------------------
    // Store : admin peut créer
    // -------------------------------------------------------

    public function test_admin_can_store_service(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/lab/services', [
                'title' => 'Animation créativité',
                'category' => 'Facilitation',
                'description' => 'Un atelier pour stimuler la créativité collective.',
            ])
            ->assertRedirect('/lab/services')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('lab_services', [
            'title' => 'Animation créativité',
            'category' => 'Facilitation',
            'created_by' => $admin->id,
        ]);
    }

    public function test_adherent_cannot_store_service(): void
    {
        $adherent = User::factory()->create();

        $this->actingAs($adherent)
            ->post('/lab/services', [
                'title' => 'Test',
                'category' => 'Autre',
                'description' => 'Description.',
            ])
            ->assertForbidden();
    }

    // -------------------------------------------------------
    // Destroy : admin peut supprimer
    // -------------------------------------------------------

    public function test_admin_can_delete_service(): void
    {
        $admin = User::factory()->admin()->create();
        $service = LabService::factory()->create();

        $this->actingAs($admin)
            ->delete("/lab/services/{$service->id}")
            ->assertRedirect('/lab/services')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('lab_services', ['id' => $service->id]);
    }

    public function test_adherent_cannot_delete_service(): void
    {
        $adherent = User::factory()->create();
        $service = LabService::factory()->create();

        $this->actingAs($adherent)
            ->delete("/lab/services/{$service->id}")
            ->assertForbidden();
    }
}
