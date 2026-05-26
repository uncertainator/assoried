<?php

namespace App\Models;

use Database\Factories\MeetingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Meeting extends Model
{
    /** @use HasFactory<MeetingFactory> */
    use HasFactory;

    protected $fillable = [
        'circle_id',
        'created_by',
        'title',
        'scheduled_at',
        'duration_minutes',
        'location',
        'visio_url',
        'is_public',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function agendaItems(): HasMany
    {
        return $this->hasMany(MeetingAgendaItem::class)->orderBy('position');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(MeetingReport::class)->orderByDesc('created_at');
    }

    public function publishedReport(): HasOne
    {
        return $this->hasOne(MeetingReport::class)->where('status', 'published');
    }

    public function isPast(): bool
    {
        return $this->scheduled_at->isPast();
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }
}
