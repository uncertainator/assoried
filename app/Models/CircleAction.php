<?php

namespace App\Models;

use App\Enums\CircleActionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CircleAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'circle_id',
        'author_id',
        'title',
        'description',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'status' => CircleActionStatus::class,
    ];

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('due_date', '>=', today());
    }
}
