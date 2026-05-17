<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChooseParcoursOptionRequest;
use App\Models\ParcoursOption;
use App\Models\ParcoursQuestion;
use App\Models\ParcoursService;
use App\Services\ParcoursNavigator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParcoursController extends Controller
{
    public function __construct(private readonly ParcoursNavigator $navigator) {}

    public function start(): RedirectResponse
    {
        session()->forget('parcours.history');

        $root = $this->navigator->getRootQuestion();

        if ($root === null) {
            return redirect()->route('parcours.fallback');
        }

        return redirect()->route('parcours.step', $root);
    }

    public function step(ParcoursQuestion $question): View
    {
        $history = session('parcours.history', []);
        $preselectedOptionId = $this->navigator->getPreselectedOption($question->id, $history);

        return view('public.parcours.step', [
            'question' => $question->load('options'),
            'preselectedOptionId' => $preselectedOptionId,
            'hasHistory' => count($history) > 0,
        ]);
    }

    public function choose(ChooseParcoursOptionRequest $request, ParcoursQuestion $question): RedirectResponse
    {
        $option = ParcoursOption::findOrFail($request->validated('option_id'));

        $history = session('parcours.history', []);
        $history[] = ['question_id' => $question->id, 'option_id' => $option->id];
        session(['parcours.history' => $history]);

        $option->load('nextQuestion', 'service');
        $resolution = $this->navigator->resolveOption($option);

        return match ($resolution['type']) {
            'question' => redirect()->route('parcours.step', $resolution['target']),
            'service' => redirect()->route('parcours.result', $resolution['target']),
            default => redirect()->route('parcours.fallback'),
        };
    }

    public function back(): RedirectResponse
    {
        $history = session('parcours.history', []);

        if (empty($history)) {
            return redirect()->route('parcours.start');
        }

        $last = array_pop($history);
        session(['parcours.history' => $history]);

        return redirect()->route('parcours.step', $last['question_id']);
    }

    public function result(ParcoursService $service): View
    {
        $hasHistory = count(session('parcours.history', [])) > 0;

        return view('public.parcours.result', compact('service', 'hasHistory'));
    }

    public function fallback(): View
    {
        $hasHistory = count(session('parcours.history', [])) > 0;

        return view('public.parcours.fallback', compact('hasHistory'));
    }
}
