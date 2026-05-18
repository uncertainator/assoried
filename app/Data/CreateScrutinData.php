<?php

namespace App\Data;

use App\Enums\ScrutinMajorityType;
use App\Enums\ScrutinQuorumType;

readonly class CreateScrutinData
{
    public function __construct(
        public string $title,
        public ?string $description,
        public string $opened_at,
        public string $closes_at,
        public ScrutinQuorumType $quorum_type,
        public float $quorum_value,
        public ScrutinMajorityType $majority_type,
        public ?float $majority_threshold,
        public array $options,
        public int $created_by,
    ) {}
}
