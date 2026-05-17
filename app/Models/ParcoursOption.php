<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParcoursOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'label',
        'next_question_id',
        'service_id',
        'sort_order',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(ParcoursQuestion::class, 'question_id');
    }

    public function nextQuestion(): BelongsTo
    {
        return $this->belongsTo(ParcoursQuestion::class, 'next_question_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(ParcoursService::class, 'service_id');
    }

    public function isConfigured(): bool
    {
        return $this->next_question_id !== null || $this->service_id !== null;
    }

    public function leadsToService(): bool
    {
        return $this->service_id !== null;
    }
}
