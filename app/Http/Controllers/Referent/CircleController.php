<?php

namespace App\Http\Controllers\Referent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Referent\CircleUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CircleController extends Controller
{
    public function edit(): View|RedirectResponse
    {
        $circle = Auth::user()->assignedCircle()->first();

        if (! $circle) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Vous n\'avez pas de cercle assigné.');
        }

        $this->authorize('update', $circle);

        return view('referent.circle.edit', compact('circle'));
    }

    public function update(CircleUpdateRequest $request): RedirectResponse
    {
        $circle = Auth::user()->assignedCircle()->first();

        if (! $circle) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Vous n\'avez pas de cercle assigné.');
        }

        $this->authorize('update', $circle);

        $circle->update($request->validated());

        return redirect()->route('referent.circle.edit')
            ->with('success', 'Les informations du cercle ont été mises à jour.');
    }
}
