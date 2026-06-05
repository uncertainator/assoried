<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordLoginController extends Controller
{
    public function store(Request $request)
    {
        $request->validateWithBag('password', [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($request->email));

        $user = User::where('email', $email)->first();

        if ($user && is_null($user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(
                    ['email' => 'Ce compte n\'a pas de mot de passe. Utilisez plutôt le lien magique.'],
                    'password'
                );
        }

        if ($user && $user->isPending()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(
                    ['email' => 'Votre adhésion est en cours de validation par le bureau.'],
                    'password'
                );
        }

        if ($user && $user->isRejected()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(
                    ['email' => 'Votre demande d\'adhésion n\'a pas été retenue.'],
                    'password'
                );
        }

        if (! Auth::attempt(['email' => $email, 'password' => $request->password], $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(
                    ['email' => 'Identifiants invalides.'],
                    'password'
                );
        }

        $request->session()->regenerate();

        $destination = Auth::user()->isAdmin() ? route('admin.index') : route('member.dashboard');

        return redirect()->intended($destination);
    }
}
