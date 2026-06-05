<?php

namespace Tests\Feature\Parcours;

use App\Models\ParcoursOption;
use App\Models\ParcoursQuestion;
use App\Models\ParcoursService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParcoursAdminTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // 1. Admin voit la liste des questions et services
    // ---------------------------------------------------------------
    public function test_admin_can_list_questions_and_services(): void
    {
        $admin = User::factory()->admin()->create();
        ParcoursQuestion::factory()->create(['label' => 'Ma question test']);
        ParcoursService::factory()->create(['name' => 'Mon service test']);

        $response = $this->actingAs($admin)->get(route('admin.parcours.index'));

        $response->assertOk();
        $response->assertSee('Ma question test');
        $response->assertSee('Mon service test');
    }

    // ---------------------------------------------------------------
    // 2. Admin crée un service
    // ---------------------------------------------------------------
    public function test_admin_can_create_service(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.parcours.services.store'), [
            'name' => 'Co-développement',
            'description' => 'Un service de co-développement.',
            'use_cases' => 'Cas d\'usage.',
            'cta_type' => 'contact',
            'cta_value' => 'https://example.com',
            'sort_order' => 0,
        ]);

        $response->assertRedirect(route('admin.parcours.index'));
        $this->assertDatabaseHas('parcours_services', [
            'name' => 'Co-développement',
            'created_by' => $admin->id,
        ]);
    }

    // ---------------------------------------------------------------
    // 3. Admin modifie un service
    // ---------------------------------------------------------------
    public function test_admin_can_update_service(): void
    {
        $admin = User::factory()->admin()->create();
        $service = ParcoursService::factory()->create(['name' => 'Ancien nom']);

        $response = $this->actingAs($admin)->put(route('admin.parcours.services.update', $service), [
            'name' => 'Nouveau nom',
            'description' => $service->description,
            'use_cases' => implode("\n", $service->use_cases),
            'cta_type' => $service->cta_type->value,
            'cta_value' => $service->cta_value,
        ]);

        $response->assertRedirect(route('admin.parcours.index'));
        $this->assertDatabaseHas('parcours_services', ['id' => $service->id, 'name' => 'Nouveau nom']);
    }

    // ---------------------------------------------------------------
    // 4. Admin supprime un service
    // ---------------------------------------------------------------
    public function test_admin_can_delete_service(): void
    {
        $admin = User::factory()->admin()->create();
        $service = ParcoursService::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.parcours.services.destroy', $service));

        $response->assertRedirect(route('admin.parcours.index'));
        $this->assertDatabaseMissing('parcours_services', ['id' => $service->id]);
    }

    // ---------------------------------------------------------------
    // 5. Admin crée une question avec options
    // ---------------------------------------------------------------
    public function test_admin_can_create_question_with_options(): void
    {
        $admin = User::factory()->admin()->create();
        $service = ParcoursService::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.parcours.questions.store'), [
            'label' => 'Quel est votre contexte ?',
            'sort_order' => 0,
            'options' => [
                ['label' => 'Option A', 'service_id' => $service->id, 'sort_order' => 0],
                ['label' => 'Option B', 'sort_order' => 1],
            ],
        ]);

        $response->assertRedirect(route('admin.parcours.index'));
        $this->assertDatabaseHas('parcours_questions', ['label' => 'Quel est votre contexte ?']);

        $question = ParcoursQuestion::where('label', 'Quel est votre contexte ?')->first();
        $this->assertCount(2, $question->options);
        $this->assertEquals($service->id, $question->options->first()->service_id);
    }

    // ---------------------------------------------------------------
    // 6. Admin définit la question racine (l'ancienne repasse à false)
    // ---------------------------------------------------------------
    public function test_admin_can_set_root_question(): void
    {
        $admin = User::factory()->admin()->create();
        $oldRoot = ParcoursQuestion::factory()->root()->create();
        $newRoot = ParcoursQuestion::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.parcours.questions.set-root', $newRoot));

        $response->assertRedirect(route('admin.parcours.index'));
        $this->assertDatabaseHas('parcours_questions', ['id' => $newRoot->id, 'is_root' => true]);
        $this->assertDatabaseHas('parcours_questions', ['id' => $oldRoot->id, 'is_root' => false]);
    }

    // ---------------------------------------------------------------
    // 7. Admin prévisualise l'arbre
    // ---------------------------------------------------------------
    public function test_admin_can_preview_tree(): void
    {
        $admin = User::factory()->admin()->create();
        $root = ParcoursQuestion::factory()->root()->create(['label' => 'Question racine ?']);

        $response = $this->actingAs($admin)->get(route('admin.parcours.preview'));

        $response->assertOk();
        $response->assertSee('Question racine ?');
    }

    // ---------------------------------------------------------------
    // 8. Non-admin ne peut pas accéder au back-office parcours
    // ---------------------------------------------------------------
    public function test_non_admin_cannot_access_parcours_admin_routes(): void
    {
        $adherent = User::factory()->adherent()->create();

        $this->actingAs($adherent)->get(route('admin.parcours.index'))->assertForbidden();
        $this->actingAs($adherent)->get(route('admin.parcours.services.create'))->assertForbidden();
        $this->actingAs($adherent)->get(route('admin.parcours.questions.create'))->assertForbidden();
    }

    // ---------------------------------------------------------------
    // 9. Admin modifie une question et ses options
    // ---------------------------------------------------------------
    public function test_admin_can_update_question(): void
    {
        $admin = User::factory()->admin()->create();
        $question = ParcoursQuestion::factory()->create(['label' => 'Ancien libellé']);
        $option = ParcoursOption::factory()->create(['question_id' => $question->id, 'label' => 'Option existante']);

        $response = $this->actingAs($admin)->put(route('admin.parcours.questions.update', $question), [
            'label' => 'Nouveau libellé',
            'sort_order' => 0,
            'options' => [
                ['id' => $option->id, 'label' => 'Option modifiée', 'sort_order' => 0],
            ],
        ]);

        $response->assertRedirect(route('admin.parcours.index'));
        $this->assertDatabaseHas('parcours_questions', ['id' => $question->id, 'label' => 'Nouveau libellé']);
        $this->assertDatabaseHas('parcours_options', ['id' => $option->id, 'label' => 'Option modifiée']);
    }
}
