<?php

namespace App\Actions;

use App\Enums\AccountStatus;
use App\Models\AuditLog;
use App\Models\User;
use App\Notifications\CircleLeaveNotification;
use App\Services\AnonymizeUser;
use Illuminate\Support\Facades\DB;

class ExcludeMember
{
    public function __construct(private readonly AnonymizeUser $anonymizer) {}

    /**
     * Exclude a member: record the governance trace, notify circle referents,
     * detach all circles, anonymize personal data and flag the account
     * excluded. Idempotent — a no-op if the member is already excluded.
     * Past votes and CircleActions are preserved (no hard delete).
     */
    public function handle(User $member, User $admin, ?string $reason = null): void
    {
        if ($member->isExcluded()) {
            return;
        }

        DB::transaction(function () use ($member, $admin, $reason) {
            $circles = $member->circles()->get();

            AuditLog::record(AuditLog::TYPE_MEMBER_EXCLUSION, $admin, [
                'target_user_id' => $member->id,
                'meta' => [
                    'reason' => $reason,
                    'member_name' => $member->name,
                ],
            ]);

            // Notify each circle's referent while the member's identity is intact.
            foreach ($circles as $circle) {
                if ($circle->referent) {
                    try {
                        $circle->referent->notify(new CircleLeaveNotification($member, $circle));
                    } catch (\Throwable $e) {
                        logger()->error('CircleLeaveNotification failed: '.$e->getMessage());
                    }
                }
            }

            $member->circles()->detach();

            $this->anonymizer->handle($member);

            $member->forceFill(['account_status' => AccountStatus::Excluded])->save();
        });
    }
}
