<?php

namespace App\Models;

use App\Enums\MeetingReportStatus;
use Database\Factories\MeetingReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingReport extends Model
{
    /** @use HasFactory<MeetingReportFactory> */
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'created_by',
        'status',
        'participants',
        'agenda_notes',
        'decisions',
        'open_points',
        'free_notes',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => MeetingReportStatus::class,
            'agenda_notes' => 'array',
            'decisions' => 'array',
            'open_points' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isDraft(): bool
    {
        return $this->status === MeetingReportStatus::Draft;
    }

    public function isPublished(): bool
    {
        return $this->status === MeetingReportStatus::Published;
    }
}
