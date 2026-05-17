<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreParcoursQuestionRequest;
use App\Http\Requests\UpdateParcoursQuestionRequest;
use App\Models\ParcoursQuestion;
use App\Models\ParcoursService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ParcoursQuestionController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', ParcoursQuestion::class);

        $questions = ParcoursQuestion::with('options.nextQuestion', 'options.service')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        $services = ParcoursService::orderBy('name')->get();

        return view('admin.parcours.index', compact('questions', 'services'));
    }

    public function create(): View
    {
        Gate::authorize('create', ParcoursQuestion::class);

        $questions = ParcoursQuestion::orderBy('label')->get();
        $services = ParcoursService::orderBy('name')->get();

        return view('admin.parcours.questions.create', compact('questions', 'services'));
    }

    public function store(StoreParcoursQuestionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $question = ParcoursQuestion::create([
            'label' => $data['label'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        foreach ($data['options'] ?? [] as $optionData) {
            $question->options()->create([
                'label' => $optionData['label'],
                'next_question_id' => $optionData['next_question_id'] ?? null,
                'service_id' => $optionData['service_id'] ?? null,
                'sort_order' => $optionData['sort_order'] ?? 0,
            ]);
        }

        return redirect()->route('admin.parcours.index')
            ->with('success', 'Question créée avec succès.');
    }

    public function edit(ParcoursQuestion $question): View
    {
        Gate::authorize('update', $question);

        $question->load('options');
        $questions = ParcoursQuestion::where('id', '!=', $question->id)->orderBy('label')->get();
        $services = ParcoursService::orderBy('name')->get();

        return view('admin.parcours.questions.edit', compact('question', 'questions', 'services'));
    }

    public function update(UpdateParcoursQuestionRequest $request, ParcoursQuestion $question): RedirectResponse
    {
        $data = $request->validated();

        $question->update([
            'label' => $data['label'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $submittedIds = collect($data['options'] ?? [])->pluck('id')->filter()->all();
        $question->options()->whereNotIn('id', $submittedIds)->delete();

        foreach ($data['options'] ?? [] as $optionData) {
            $question->options()->updateOrCreate(
                ['id' => $optionData['id'] ?? null],
                [
                    'label' => $optionData['label'],
                    'next_question_id' => $optionData['next_question_id'] ?? null,
                    'service_id' => $optionData['service_id'] ?? null,
                    'sort_order' => $optionData['sort_order'] ?? 0,
                    'question_id' => $question->id,
                ]
            );
        }

        return redirect()->route('admin.parcours.index')
            ->with('success', 'Question mise à jour.');
    }

    public function destroy(ParcoursQuestion $question): RedirectResponse
    {
        Gate::authorize('delete', $question);

        $question->delete();

        return redirect()->route('admin.parcours.index')
            ->with('success', 'Question supprimée.');
    }

    public function setRoot(ParcoursQuestion $question): RedirectResponse
    {
        Gate::authorize('update', $question);

        ParcoursQuestion::where('is_root', true)->update(['is_root' => false]);
        $question->update(['is_root' => true]);

        return redirect()->route('admin.parcours.index')
            ->with('success', 'Question racine définie.');
    }

    public function preview(): View
    {
        Gate::authorize('viewAny', ParcoursQuestion::class);

        $root = ParcoursQuestion::root()
            ->with('options.nextQuestion.options.nextQuestion', 'options.service')
            ->first();

        return view('admin.parcours.preview', compact('root'));
    }
}
