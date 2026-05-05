<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CircleController extends Controller
{
    public function index()
    {
        $circles = Circle::where('is_active', true)->withCount('users')->get();
        $myCircleIds = Auth::user()->circles()->pluck('circles.id');

        return view('member.circles.index', compact('circles', 'myCircleIds'));
    }

    public function join(Request $request, Circle $circle)
    {
        $user = Auth::user();

        if (! $circle->is_active) {
            return back()->with('error', 'Ce cercle n\'est pas disponible.');
        }

        if ($circle->isFull()) {
            return back()->with('error', 'Ce cercle est complet.');
        }

        if ($user->circles()->where('circle_id', $circle->id)->exists()) {
            return back()->with('error', 'Vous êtes déjà dans ce cercle.');
        }

        $user->circles()->attach($circle->id, ['joined_at' => now()]);

        return back()->with('success', 'Vous avez rejoint le cercle « ' . $circle->name . ' ».');
    }

    public function leave(Circle $circle)
    {
        Auth::user()->circles()->detach($circle->id);

        return back()->with('success', 'Vous avez quitté le cercle « ' . $circle->name . ' ».');
    }
}
