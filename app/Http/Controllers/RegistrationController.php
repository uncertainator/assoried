<?php

namespace App\Http\Controllers;

use App\Mail\MagicLinkMail;
use App\Models\Circle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class RegistrationController extends Controller
{
    public function show()
    {
        $circles = Circle::where('is_active', true)->get();

        return view('inscription', compact('circles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'     => ['required', 'email'],
            'circles'   => ['nullable', 'array'],
            'circles.*' => ['integer', 'exists:circles,id'],
        ]);

        $email = strtolower(trim($request->email));

        $url = URL::temporarySignedRoute(
            'auth.magic.verify',
            now()->addMinutes(15),
            [
                'email'   => $email,
                'circles' => $request->input('circles', []),
            ]
        );

        Mail::to($email)->send(new MagicLinkMail($url));

        return redirect()->route('auth.link-sent')->with('email', $email);
    }
}
