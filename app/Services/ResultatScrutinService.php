<?php

namespace App\Services;

use App\Data\ScrutinResultData;
use App\Enums\ScrutinMajorityType;
use App\Enums\ScrutinQuorumType;
use App\Enums\ScrutinResultStatus;
use App\Enums\UserRole;
use App\Models\Scrutin;
use App\Models\User;

class ResultatScrutinService
{
    public function compute(Scrutin $scrutin): ScrutinResultData
    {
        $voteCounts = $scrutin->votes()
            ->selectRaw('scrutin_option_id, count(*) as cnt')
            ->groupBy('scrutin_option_id')
            ->pluck('cnt', 'scrutin_option_id')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        $totalVotes = array_sum($voteCounts);

        $activeMembersCount = User::whereIn('role', [
            UserRole::Adherent->value,
            UserRole::Referent->value,
        ])->count();

        $quorumRequired = match ($scrutin->quorum_type) {
            ScrutinQuorumType::Fixed => (int) $scrutin->quorum_value,
            ScrutinQuorumType::Proportional => (int) ceil($activeMembersCount * (float) $scrutin->quorum_value / 100),
        };

        $quorumReached = $totalVotes >= $quorumRequired;

        if (! $quorumReached) {
            return new ScrutinResultData(
                result_status: ScrutinResultStatus::QuorumNotReached,
                total_votes: $totalVotes,
                active_members_at_close: $activeMembersCount,
                quorum_reached: false,
                winning_option_id: null,
                vote_counts: $voteCounts,
            );
        }

        if (empty($voteCounts)) {
            return new ScrutinResultData(
                result_status: ScrutinResultStatus::NoDecision,
                total_votes: 0,
                active_members_at_close: $activeMembersCount,
                quorum_reached: true,
                winning_option_id: null,
                vote_counts: [],
            );
        }

        arsort($voteCounts);
        $winningOptionId = (int) array_key_first($voteCounts);
        $winningVoteCount = $voteCounts[$winningOptionId];

        $threshold = match ($scrutin->majority_type) {
            ScrutinMajorityType::Simple => $totalVotes / 2,
            ScrutinMajorityType::Qualified => $totalVotes * (float) $scrutin->majority_threshold / 100,
        };

        $adopted = $winningVoteCount > $threshold;

        return new ScrutinResultData(
            result_status: $adopted ? ScrutinResultStatus::Adopted : ScrutinResultStatus::NoDecision,
            total_votes: $totalVotes,
            active_members_at_close: $activeMembersCount,
            quorum_reached: true,
            winning_option_id: $adopted ? $winningOptionId : null,
            vote_counts: $voteCounts,
        );
    }
}
