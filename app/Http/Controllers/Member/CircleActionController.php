<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCircleActionRequest;
use App\Http\Requests\UpdateCircleActionRequest;
use App\Models\Circle;
use App\Models\CircleAction;
use Illuminate\Http\RedirectResponse;

class CircleActionController extends Controller
{
    public function store(StoreCircleActionRequest $request, Circle $circle): RedirectResponse
    {
        $circle->actions()->create([
            'author_id' => $request->user()->id,
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'due_date' => $request->validated('due_date'),
        ]);

        return redirect()->route('member.circles.show', $circle)
            ->with('success', 'Action créée avec succès.');
    }

    public function update(UpdateCircleActionRequest $request, CircleAction $action): RedirectResponse
    {
        $action->update(['status' => $request->validated('status')]);

        return redirect()->route('member.circles.show', $action->circle)
            ->with('success', 'Statut mis à jour.');
    }

    public function destroy(CircleAction $action): RedirectResponse
    {
        $this->authorize('delete', $action);

        $circle = $action->circle;
        $action->delete();

        return redirect()->route('member.circles.show', $circle)
            ->with('success', 'Action supprimée.');
    }
}
