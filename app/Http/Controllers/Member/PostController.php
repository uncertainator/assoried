<?php

namespace App\Http\Controllers\Member;

use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Circle;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Circle $circle): View
    {
        $circle->load([
            'referent',
            'events' => fn ($q) => $q->orderBy('starts_at'),
            'actions' => fn ($q) => $q->orderBy('due_date'),
        ]);

        $upcomingEvents = $circle->events->filter(fn ($e) => $e->starts_at->isFuture())->values();
        $pastEvents = $circle->events->filter(fn ($e) => $e->starts_at->isPast())->sortByDesc('starts_at')->values();
        $actions = $circle->actions;

        $membership = Auth::user()->memberships()
            ->where('circle_id', $circle->id)
            ->where('status', MembershipStatus::Approved)
            ->first();

        $posts = $circle->posts()->with('author')->latest()->paginate(15);

        return view('member.circles.show', compact('circle', 'posts', 'upcomingEvents', 'pastEvents', 'actions', 'membership'));
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
