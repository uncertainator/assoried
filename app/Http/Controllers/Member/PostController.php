<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Circle;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Circle $circle): View
    {
        $posts = $circle->posts()->with('author')->latest()->paginate(15);

        return view('member.circles.show', compact('circle', 'posts'));
    }

    public function store(StorePostRequest $request, Circle $circle): RedirectResponse
    {
        $push = $request->boolean('push_to_general');

        $circle->posts()->create([
            'author_id' => $request->user()->id,
            'body' => $request->validated('body'),
            'pushed_to_general' => $push,
            'pushed_at' => $push ? now() : null,
        ]);

        return redirect()->route('member.circles.show', $circle)
            ->with('success', 'Publication ajoutée.');
    }

    public function pushToGeneral(Post $post): RedirectResponse
    {
        $this->authorize('pushToGeneral', $post);

        $post->update([
            'pushed_to_general' => true,
            'pushed_at' => now(),
        ]);

        return back()->with('success', 'Post poussé dans le feed général.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $circle = $post->circle;
        $post->delete();

        return redirect()->route('member.circles.show', $circle)
            ->with('success', 'Publication supprimée.');
    }
}
