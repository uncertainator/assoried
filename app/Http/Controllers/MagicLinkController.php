<?php

namespace App\Http\Controllers;

use App\Mail\MagicLinkMail;
use App\Mail\MembershipPendingMail;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class MagicLinkController extends Controller
{
    public function showForm()
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }

        return view('auth.login');
    }

    public function send(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'circles' => ['nullable', 'array'],
            'circles.*' => ['integer', 'exists:circles,id'],
        ]);

        $email = strtolower(trim($request->email));

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

    public function verify(Request $request)
    {
        if (! $request->hasValidSignature()) {
            return redirect()->route('auth.link-invalid');
        }

        $email = strtolower(trim($request->query('email')));
        $circleIds = (array) $request->query('circles', []);

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => '']
        );

        if ($circleIds) {
            $circles = Circle::whereIn('id', $circleIds)->where('is_active', true)->get();
            foreach ($circles as $circle) {
                if (! $circle->isFull() && ! $user->circles()->where('circle_id', $circle->id)->exists()) {
                    $user->circles()->attach($circle->id, ['joined_at' => now()]);
                }
            }
        }

        // Nouvelle demande d'adhésion : on prévient le candidat, pas de connexion.
        if ($user->wasRecentlyCreated) {
            Mail::to($user->email)->send(new MembershipPendingMail);

            return redirect()->route('auth.membership-pending');
        }

        // Compte en attente de validation ou refusé : connexion bloquée.
        if (! $user->isActive()) {
            return redirect()->route('login')->withErrors([
                'email' => $user->isPending()
                    ? 'Votre adhésion est en cours de validation par le bureau.'
                    : 'Votre demande d\'adhésion n\'a pas été retenue.',
            ]);
        }

        Auth::login($user, remember: true);

        if ($user->needsPasswordSetup()) {
            return redirect()->route('account.password.setup');
        }

        return $user->isAdmin()
            ? redirect()->route('admin.index')
            : redirect()->route('member.dashboard');
    }
}
