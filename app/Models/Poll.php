<?php

namespace App\Models;

use App\Enums\PollType;
use Database\Factories\PollFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    /** @use HasFactory<PollFactory> */
    use HasFactory;

    protected $fillable = ['circle_id', 'created_by', 'title', 'type', 'options', 'closes_at'];

    protected function casts(): array
    {
        return [
            'type' => PollType::class,
            'options' => 'array',
            'closes_at' => 'datetime',
        ];
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function isClosed(): bool
    {
        return $this->closes_at->isPast();
    }

    public function hasVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }

    public function results(): array
    {
        if (! $this->isClosed()) {
            return [];
        }

        $total = $this->votes()->count();
        $counts = $this->votes()
            ->selectRaw('choice, count(*) as count')
            ->groupBy('choice')
            ->pluck('count', 'choice')
            ->toArray();

        if ($this->type === PollType::YesNo) {
            $breakdown = [
                'oui' => (int) ($counts['oui'] ?? 0),
                'non' => (int) ($counts['non'] ?? 0),
            ];
        } else {
            $breakdown = [];
            foreach ($this->options ?? [] as $option) {
                $breakdown[$option] = (int) ($counts[$option] ?? 0);
            }
        }

        return ['total' => $total, 'breakdown' => $breakdown];
    }
}
