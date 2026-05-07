<?php

namespace App\Http\Controllers;

use App\Mail\MagicLinkMail;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

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
            'email' => ['required', 'email'],
            'auth_method' => ['required', 'in:magic_link,password'],
            'password' => $request->auth_method === 'password'
                ? ['required', Password::min(8), 'confirmed']
                : ['nullable'],
            'circles' => ['nullable', 'array'],
            'circles.*' => ['integer', 'exists:circles,id'],
        ]);

        $email = strtolower(trim($request->email));

        if ($request->auth_method === 'password') {
            $user = User::where('email', $email)->first();

            if ($user && ! is_null($user->password)) {
                // Compte existant avec mot de passe : rediriger vers la connexion
                return redirect()->route('login')
                    ->with('info', 'Vous avez déjà un compte. Connectez-vous directement.');
            }

            if ($user) {
                // Compte magic link sans mot de passe : on lui ajoute un mot de passe
                $user->update(['password' => $request->password]);
            } else {
                // Nouveau compte
                $user = User::create(['email' => $email, 'name' => '', 'password' => $request->password]);
                event(new Registered($user));
            }

            $this->attachCircles($user, (array) $request->input('circles', []));

            Auth::login($user, remember: true);

            return redirect()->route('member.dashboard');
        }

        $url = URL::temporarySignedRoute(
            'auth.magic.verify',
            now()->addMinutes(15),
            [
                'email' => $email,
                'circles' => $request->input('circles', []),
            ]
        );

        Mail::to($email)->send(new MagicLinkMail($url));

        return redirect()->route('auth.link-sent')->with('email', $email);
    }

    private function attachCircles(User $user, array $circleIds): void
    {
        if (empty($circleIds)) {
            return;
        }

        $circles = Circle::whereIn('id', $circleIds)->where('is_active', true)->get();

        foreach ($circles as $circle) {
            if (! $circle->isFull() && ! $user->circles()->where('circle_id', $circle->id)->exists()) {
                $user->circles()->attach($circle->id, ['joined_at' => now()]);
            }
        }
    }
}
