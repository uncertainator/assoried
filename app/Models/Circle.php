<?php

namespace App\Models;

use Database\Factories\CircleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Circle extends Model
{
    /** @use HasFactory<CircleFactory> */
    use HasFactory;

    protected $fillable = ['slug', 'name', 'description', 'max_members', 'is_active', 'referent_id'];

    protected $casts = ['is_active' => 'boolean'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('joined_at');
    }

    public function referent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referent_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(CircleMembership::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(CircleAction::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(CircleJournalEntry::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CircleDocument::class)->latest('document_date');
    }

    public function polls(): HasMany
    {
        return $this->hasMany(Poll::class);
    }

    public function isManagedBy(User $user): bool
    {
        return $user->isAdmin() || $this->referent_id === $user->id;
    }

    public function isFull(): bool
    {
        if ($this->max_members === null) {
            return false;
        }

        return $this->users()->count() >= $this->max_members;
    }
}
