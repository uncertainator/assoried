<?php

namespace App\Services;

use App\Models\ParcoursOption;
use App\Models\ParcoursQuestion;

class ParcoursNavigator
{
    public function getRootQuestion(): ?ParcoursQuestion
    {
        return ParcoursQuestion::root()->first();
    }

    /**
     * Resolve where an option leads.
     *
     * Returns ['type' => 'question'|'service'|'fallback', 'target' => model|null]
     */
    public function resolveOption(ParcoursOption $option): array
    {
        if ($option->next_question_id !== null) {
            return ['type' => 'question', 'target' => $option->nextQuestion];
        }

        if ($option->service_id !== null) {
            return ['type' => 'service', 'target' => $option->service];
        }

        return ['type' => 'fallback', 'target' => null];
    }

    /**
     * Find the option previously chosen for a given question in the navigation history.
     *
     * History format: [['question_id' => int, 'option_id' => int], ...]
     */
    public function getPreselectedOption(int $questionId, array $history): ?int
    {
        foreach ($history as $entry) {
            if (($entry['question_id'] ?? null) === $questionId) {
                return $entry['option_id'] ?? null;
            }
        }

        return null;
    }
}
