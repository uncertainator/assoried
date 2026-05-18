<?php

namespace App\Models;

use App\Enums\ScrutinMajorityType;
use App\Enums\ScrutinQuorumType;
use App\Enums\ScrutinResultStatus;
use App\Enums\ScrutinStatus;
use Database\Factories\ScrutinFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scrutin extends Model
{
    /** @use HasFactory<ScrutinFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'opened_at',
        'closes_at',
        'quorum_type',
        'quorum_value',
        'majority_type',
        'majority_threshold',
        'status',
        'created_by',
        'result_status',
        'total_votes',
        'winning_option_id',
        'active_members_at_close',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closes_at' => 'datetime',
            'quorum_type' => ScrutinQuorumType::class,
            'majority_type' => ScrutinMajorityType::class,
            'status' => ScrutinStatus::class,
            'result_status' => ScrutinResultStatus::class,
            'quorum_value' => 'decimal:2',
            'majority_threshold' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ScrutinOption::class)->orderBy('position');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ScrutinVote::class);
    }

    public function winningOption(): BelongsTo
    {
        return $this->belongsTo(ScrutinOption::class, 'winning_option_id');
    }

    public function isOpen(): bool
    {
        return $this->status === ScrutinStatus::Open;
    }

    public function isClosed(): bool
    {
        return $this->status === ScrutinStatus::Closed;
    }

    public function isCancelled(): bool
    {
        return $this->status === ScrutinStatus::Cancelled;
    }

    public function isEditable(): bool
    {
        return $this->status === ScrutinStatus::Draft;
    }

    public function isVotable(): bool
    {
        return $this->isOpen()
            && $this->opened_at !== null
            && $this->opened_at->isPast()
            && $this->closes_at !== null
            && $this->closes_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->isOpen()
            && $this->closes_at !== null
            && $this->closes_at->isPast();
    }

    public function hasVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }

    public function canBeCancelled(): bool
    {
        return $this->isEditable()
            || ($this->isOpen() && $this->votes()->count() === 0);
    }
}
