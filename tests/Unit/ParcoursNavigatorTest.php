<?php

namespace Tests\Unit;

use App\Models\ParcoursOption;
use App\Models\ParcoursQuestion;
use App\Models\ParcoursService;
use App\Services\ParcoursNavigator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParcoursNavigatorTest extends TestCase
{
    use RefreshDatabase;

    private ParcoursNavigator $navigator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->navigator = new ParcoursNavigator;
    }

    // ---------------------------------------------------------------
    // 1. resolveOption → type question
    // ---------------------------------------------------------------
    public function test_resolve_option_returns_question_type(): void
    {
        $nextQuestion = ParcoursQuestion::factory()->create();
        $option = ParcoursOption::factory()->toQuestion($nextQuestion->id)->create();
        $option->load('nextQuestion');

        $result = $this->navigator->resolveOption($option);

        $this->assertEquals('question', $result['type']);
        $this->assertInstanceOf(ParcoursQuestion::class, $result['target']);
        $this->assertEquals($nextQuestion->id, $result['target']->id);
    }

    // ---------------------------------------------------------------
    // 2. resolveOption → type service
    // ---------------------------------------------------------------
    public function test_resolve_option_returns_service_type(): void
    {
        $service = ParcoursService::factory()->create();
        $option = ParcoursOption::factory()->toService($service->id)->create();
        $option->load('service');

        $result = $this->navigator->resolveOption($option);

        $this->assertEquals('service', $result['type']);
        $this->assertInstanceOf(ParcoursService::class, $result['target']);
        $this->assertEquals($service->id, $result['target']->id);
    }

    // ---------------------------------------------------------------
    // 3. resolveOption → type fallback
    // ---------------------------------------------------------------
    public function test_resolve_option_returns_fallback_type(): void
    {
        $option = ParcoursOption::factory()->unconfigured()->create();

        $result = $this->navigator->resolveOption($option);

        $this->assertEquals('fallback', $result['type']);
        $this->assertNull($result['target']);
    }

    // ---------------------------------------------------------------
    // 4. getRootQuestion retourne la question racine
    // ---------------------------------------------------------------
    public function test_get_root_question_returns_root(): void
    {
        ParcoursQuestion::factory()->create(['is_root' => false]);
        $root = ParcoursQuestion::factory()->root()->create();

        $result = $this->navigator->getRootQuestion();

        $this->assertNotNull($result);
        $this->assertEquals($root->id, $result->id);
    }

    // ---------------------------------------------------------------
    // 5. getRootQuestion retourne null si aucune racine
    // ---------------------------------------------------------------
    public function test_get_root_question_returns_null_when_none(): void
    {
        ParcoursQuestion::factory()->create(['is_root' => false]);

        $result = $this->navigator->getRootQuestion();

        $this->assertNull($result);
    }

    // ---------------------------------------------------------------
    // 6. getPreselectedOption trouve l'option dans l'historique
    // ---------------------------------------------------------------
    public function test_get_preselected_option_finds_option_in_history(): void
    {
        $history = [
            ['question_id' => 1, 'option_id' => 3],
            ['question_id' => 4, 'option_id' => 9],
        ];

        $result = $this->navigator->getPreselectedOption(4, $history);

        $this->assertEquals(9, $result);
    }

    // ---------------------------------------------------------------
    // 7. getPreselectedOption retourne null si question absente
    // ---------------------------------------------------------------
    public function test_get_preselected_option_returns_null_when_not_in_history(): void
    {
        $history = [
            ['question_id' => 1, 'option_id' => 3],
        ];

        $result = $this->navigator->getPreselectedOption(99, $history);

        $this->assertNull($result);
    }
}
