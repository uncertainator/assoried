<?php

namespace App\Http\Controllers;

use App\Models\Circle;
use App\Models\Consultation;
use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        $circles = Circle::where('is_active', true)->get();

        $consultations = Consultation::where('masque', false)
            ->where(function ($q) {
                $q->whereNull('date_cloture')->orWhere('date_cloture', '>', now());
            })
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $heroEvent = Event::with('circle')->upcoming()->where('is_public', true)->first();
        $upcomingEvents = Event::with('circle')->upcoming()->where('is_public', true)->limit(3)->get();

        return view('home', compact('circles', 'consultations', 'heroEvent', 'upcomingEvents'));
    }
}
