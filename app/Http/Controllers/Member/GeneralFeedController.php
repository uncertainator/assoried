<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\View\View;

class GeneralFeedController extends Controller
{
    public function index(): View
    {
        $posts = Post::with(['author', 'circle'])
            ->where('pushed_to_general', true)
            ->latest('pushed_at')
            ->paginate(20);

        return view('member.feed.index', compact('posts'));
    }
}
