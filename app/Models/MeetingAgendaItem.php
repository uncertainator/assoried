<?php

namespace App\Models;

use Database\Factories\MeetingAgendaItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingAgendaItem extends Model
{
    /** @use HasFactory<MeetingAgendaItemFactory> */
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'position',
        'title',
        'duration_minutes',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }
}
