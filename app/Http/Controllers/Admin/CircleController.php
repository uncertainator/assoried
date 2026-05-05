<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use Illuminate\Http\Request;

class CircleController extends Controller
{
    public function index()
    {
        $circles = Circle::withCount('users')->orderBy('name')->get();

        return view('admin.circles.index', compact('circles'));
    }

    public function create()
    {
        return view('admin.circles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug'        => ['required', 'string', 'max:60', 'unique:circles,slug', 'regex:/^[a-z0-9\-]+$/'],
            'name'        => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'max_members' => ['nullable', 'integer', 'min:1'],
            'is_active'   => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Circle::create($data);

        return redirect()->route('admin.circles.index')->with('success', 'Cercle créé.');
    }

    public function edit(Circle $circle)
    {
        return view('admin.circles.edit', compact('circle'));
    }

    public function update(Request $request, Circle $circle)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'max_members' => ['nullable', 'integer', 'min:1'],
            'is_active'   => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $circle->update($data);

        return redirect()->route('admin.circles.index')->with('success', 'Cercle mis à jour.');
    }
}
