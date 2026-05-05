<?php

namespace App\Http\Controllers;

use App\Models\Circle;

class HomeController extends Controller
{
    public function index()
    {
        $circles = Circle::where('is_active', true)->get();

        return view('home', compact('circles'));
    }
}
