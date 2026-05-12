<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class PublicAgendaController extends Controller
{
    public function index(): View
    {
        $events = Event::with('circle')
            ->upcoming()
            ->where('is_public', true)
            ->get();

        return view('public.agenda', compact('events'));
    }
}
