<?php

namespace App\Data;

use App\Enums\ScrutinResultStatus;

readonly class ScrutinResultData
{
    public function __construct(
        public ScrutinResultStatus $result_status,
        public int $total_votes,
        public int $active_members_at_close,
        public bool $quorum_reached,
        public ?int $winning_option_id,
        public array $vote_counts,
    ) {}
}
