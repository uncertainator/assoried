<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class PasswordSetupController extends Controller
{
    public function show()
    {
        return view('auth.password.setup');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', Password::min(8), 'confirmed'],
        ]);

        Auth::user()->update(['password' => $request->password]);

        return redirect()->route('member.dashboard');
    }

    public function dismiss(Request $request)
    {
        if ($request->boolean('dont_show_again')) {
            Auth::user()->update(['password_setup_dismissed_at' => now()]);
        }

        return redirect()->route('member.dashboard');
    }
}
