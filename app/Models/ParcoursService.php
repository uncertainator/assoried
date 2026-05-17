<?php

namespace App\Models;

use App\Enums\ParcoursCtaType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParcoursService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'use_cases',
        'cta_type',
        'cta_value',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'cta_type' => ParcoursCtaType::class,
        'is_active' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(ParcoursOption::class, 'service_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
