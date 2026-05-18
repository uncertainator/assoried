<?php

namespace App\Http\Controllers;

use App\Models\Circle;
use App\Models\Consultation;

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

        return view('home', compact('circles', 'consultations'));
    }
}
