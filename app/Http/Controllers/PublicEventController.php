<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicEventController extends Controller
{
    public function show(Event $event): View
    {
        abort_unless($event->is_public, 404);

        $alreadyRegistered = false;

        if (auth()->check()) {
            $alreadyRegistered = $event->registrations()
                ->where('user_id', auth()->id())
                ->exists();
        }

        return view('public.event-show', compact('event', 'alreadyRegistered'));
    }

    public function register(Request $request, Event $event): RedirectResponse
    {
        abort_unless($event->is_public, 404);

        if (auth()->check()) {
            $user = $request->user();

            try {
                EventRegistration::create([
                    'event_id' => $event->id,
                    'user_id'  => $user->id,
                    'name'     => $user->name ?: null,
                    'email'    => $user->email,
                ]);
            } catch (\Illuminate\Database\UniqueConstraintViolationException) {
                return redirect()->route('evenements.show', $event)
                    ->with('info', 'Vous êtes déjà inscrit à cet événement.');
            }
        } else {
            $request->validate([
                'name'  => ['required', 'string', 'max:150'],
                'email' => ['required', 'email', 'max:200'],
            ], [
                'name.required'  => 'Le prénom et nom sont obligatoires.',
                'email.required' => 'L\'adresse email est obligatoire.',
                'email.email'    => 'L\'adresse email est invalide.',
            ]);

            try {
                EventRegistration::create([
                    'event_id' => $event->id,
                    'user_id'  => null,
                    'name'     => $request->name,
                    'email'    => $request->email,
                ]);
            } catch (\Illuminate\Database\UniqueConstraintViolationException) {
                return redirect()->route('evenements.show', $event)
                    ->withErrors(['email' => 'Cet email est déjà inscrit à cet événement.'])
                    ->withInput();
            }
        }

        return redirect()->route('evenements.show', $event)
            ->with('success', 'Votre inscription est enregistrée !');
    }
}
