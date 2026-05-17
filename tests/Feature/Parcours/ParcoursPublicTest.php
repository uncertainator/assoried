<?php

namespace Tests\Feature\Parcours;

use App\Models\ParcoursOption;
use App\Models\ParcoursQuestion;
use App\Models\ParcoursService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParcoursPublicTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // 1. Page de départ redirige vers la question racine
    // ---------------------------------------------------------------
    public function test_start_redirects_to_root_question(): void
    {
        $root = ParcoursQuestion::factory()->root()->create();

        $response = $this->get(route('parcours.start'));

        $response->assertRedirect(route('parcours.step', $root));
    }

    // ---------------------------------------------------------------
    // 2. Sans racine définie → redirige vers fallback
    // ---------------------------------------------------------------
    public function test_start_redirects_to_fallback_when_no_root(): void
    {
        $response = $this->get(route('parcours.start'));

        $response->assertRedirect(route('parcours.fallback'));
    }

    // ---------------------------------------------------------------
    // 3. L'étape affiche la question et ses options
    // ---------------------------------------------------------------
    public function test_step_shows_question_and_options(): void
    {
        $question = ParcoursQuestion::factory()->create(['label' => 'Quel est votre besoin ?']);
        ParcoursOption::factory()->create(['question_id' => $question->id, 'label' => 'Option A']);
        ParcoursOption::factory()->create(['question_id' => $question->id, 'label' => 'Option B']);

        $response = $this->get(route('parcours.step', $question));

        $response->assertOk();
        $response->assertSee('Quel est votre besoin ?');
        $response->assertSee('Option A');
        $response->assertSee('Option B');
    }

    // ---------------------------------------------------------------
    // 4. Parcours complet : question → option → service
    // ---------------------------------------------------------------
    public function test_visitor_navigates_complete_path_to_service(): void
    {
        $service = ParcoursService::factory()->create(['name' => 'Co-développement']);
        $question = ParcoursQuestion::factory()->root()->create();
        $option = ParcoursOption::factory()->toService($service->id)->create(['question_id' => $question->id]);

        // Démarrer
        $this->get(route('parcours.start'));

        // Choisir l'option
        $response = $this->post(route('parcours.choose', $question), ['option_id' => $option->id]);
        $response->assertRedirect(route('parcours.result', $service));

        // Voir la fiche service
        $result = $this->get(route('parcours.result', $service));
        $result->assertOk();
        $result->assertSee('Co-développement');
    }

    // ---------------------------------------------------------------
    // 5. Parcours multi-étapes : question1 → question2 → service
    // ---------------------------------------------------------------
    public function test_visitor_navigates_multi_step_path(): void
    {
        $service = ParcoursService::factory()->create(['name' => 'Design Thinking']);
        $q2 = ParcoursQuestion::factory()->create(['label' => 'Question 2']);
        $q1 = ParcoursQuestion::factory()->root()->create(['label' => 'Question 1']);
        $opt1 = ParcoursOption::factory()->toQuestion($q2->id)->create(['question_id' => $q1->id]);
        $opt2 = ParcoursOption::factory()->toService($service->id)->create(['question_id' => $q2->id]);

        $this->get(route('parcours.start'));
        $this->post(route('parcours.choose', $q1), ['option_id' => $opt1->id])
            ->assertRedirect(route('parcours.step', $q2));

        $this->post(route('parcours.choose', $q2), ['option_id' => $opt2->id])
            ->assertRedirect(route('parcours.result', $service));
    }

    // ---------------------------------------------------------------
    // 6. Retour en arrière : revient à la question précédente
    // ---------------------------------------------------------------
    public function test_visitor_can_go_back_to_previous_question(): void
    {
        $q2 = ParcoursQuestion::factory()->create();
        $q1 = ParcoursQuestion::factory()->root()->create();
        $opt1 = ParcoursOption::factory()->toQuestion($q2->id)->create(['question_id' => $q1->id]);

        $this->get(route('parcours.start'));
        $this->post(route('parcours.choose', $q1), ['option_id' => $opt1->id]);

        $response = $this->get(route('parcours.back'));
        $response->assertRedirect(route('parcours.step', $q1->id));
    }

    // ---------------------------------------------------------------
    // 7. Retour affiche l'option pré-sélectionnée
    // ---------------------------------------------------------------
    public function test_back_shows_preselected_option(): void
    {
        $q2 = ParcoursQuestion::factory()->create();
        $q1 = ParcoursQuestion::factory()->root()->create();
        $opt1 = ParcoursOption::factory()->toQuestion($q2->id)->create([
            'question_id' => $q1->id,
            'label' => 'Mon option spéciale',
        ]);

        $this->get(route('parcours.start'));
        $this->post(route('parcours.choose', $q1), ['option_id' => $opt1->id]);
        $this->get(route('parcours.back'));

        $response = $this->get(route('parcours.step', $q1));
        $response->assertOk();
        $response->assertSee('Mon option spéciale');
    }

    // ---------------------------------------------------------------
    // 8. Branche non configurée → fallback
    // ---------------------------------------------------------------
    public function test_unconfigured_branch_shows_fallback(): void
    {
        $question = ParcoursQuestion::factory()->root()->create();
        $option = ParcoursOption::factory()->unconfigured()->create(['question_id' => $question->id]);

        $this->get(route('parcours.start'));
        $response = $this->post(route('parcours.choose', $question), ['option_id' => $option->id]);
        $response->assertRedirect(route('parcours.fallback'));

        $fallback = $this->get(route('parcours.fallback'));
        $fallback->assertOk();
        $fallback->assertSee('Contactez-nous');
    }

    // ---------------------------------------------------------------
    // 9. Option invalide (n'appartient pas à la question) → 422
    // ---------------------------------------------------------------
    public function test_invalid_option_is_rejected(): void
    {
        $q1 = ParcoursQuestion::factory()->create();
        $q2 = ParcoursQuestion::factory()->create();
        $optionForQ2 = ParcoursOption::factory()->create(['question_id' => $q2->id]);

        $response = $this->post(route('parcours.choose', $q1), ['option_id' => $optionForQ2->id]);
        $response->assertSessionHasErrors('option_id');
    }

    // ---------------------------------------------------------------
    // 10. La fiche service affiche la description et le CTA
    // ---------------------------------------------------------------
    public function test_result_page_shows_service_sheet(): void
    {
        $service = ParcoursService::factory()->create([
            'name' => 'Stratégie',
            'description' => 'Une description détaillée du service.',
            'cta_type' => 'contact',
            'cta_value' => 'https://example.com/contact',
        ]);

        $response = $this->get(route('parcours.result', $service));

        $response->assertOk();
        $response->assertSee('Stratégie');
        $response->assertSee('Une description détaillée du service.');
        $response->assertSee('https://example.com/contact');
    }
}
