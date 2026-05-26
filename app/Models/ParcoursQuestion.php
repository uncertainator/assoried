<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParcoursQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'is_root',
        'show_fallback_link',
        'sort_order',
    ];

    protected $casts = [
        'is_root' => 'boolean',
        'show_fallback_link' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(ParcoursOption::class, 'question_id')->orderBy('sort_order');
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query->where('is_root', true);
    }
}
