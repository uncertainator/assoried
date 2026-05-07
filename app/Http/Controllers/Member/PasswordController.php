<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'new_password' => ['required', Password::min(8), 'confirmed'],
        ];

        if (! is_null($user->password)) {
            $rules['current_password'] = ['required', 'string'];
        }

        $request->validate($rules);

        if (! is_null($user->password) && ! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update(['password' => $request->new_password]);

        return back()->with('status', 'Mot de passe mis à jour.');
    }
}
