<?php

namespace App\Services;

use App\Enums\AccountStatus;
use App\Mail\MembershipApprovedMail;
use App\Mail\MembershipRejectedMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class MembershipApprovalService
{
    public function approve(User $user): void
    {
        $this->ensurePending($user);

        DB::transaction(function () use ($user) {
            $user->update(['account_status' => AccountStatus::Active]);
        });

        Mail::to($user->email)->send(new MembershipApprovedMail(route('login')));
    }

    public function reject(User $user, ?string $reason = null): void
    {
        $this->ensurePending($user);

        DB::transaction(function () use ($user) {
            $user->update(['account_status' => AccountStatus::Rejected]);
        });

        Mail::to($user->email)->send(new MembershipRejectedMail($reason));
    }

    private function ensurePending(User $user): void
    {
        if (! $user->isPending()) {
            throw ValidationException::withMessages([
                'account_status' => 'Cette demande a déjà été traitée.',
            ]);
        }
    }
}
