<?php

namespace App\Services;

use App\Models\User;

class AnonymizeUser
{
    /**
     * Scrub a user's personal data (RGPD). PII only — does not touch
     * account status, roles or circle memberships. The row is kept so
     * past votes/actions and the audit trail stay consistent.
     */
    public function handle(User $user): void
    {
        $user->forceFill([
            'name' => 'Membre exclu',
            'email' => 'exclu-'.$user->id.'@anonymized.invalid',
            'password' => null,
            'remember_token' => null,
            'consent_display_contact' => false,
        ])->save();
    }
}
