<?php

namespace App\Models;

use App\Enums\LabRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabInternalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'circle_id',
        'user_id',
        'lab_service_id',
        'message',
        'desired_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => LabRequestStatus::class,
            'desired_date' => 'date',
        ];
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function labService(): BelongsTo
    {
        return $this->belongsTo(LabService::class);
    }
}
