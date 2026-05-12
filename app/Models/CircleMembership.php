<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use Database\Factories\CircleMembershipFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CircleMembership extends Model
{
    /** @use HasFactory<CircleMembershipFactory> */
    use HasFactory;

    protected $table = 'circle_user';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'circle_id',
        'status',
        'joined_at',
        'validated_by',
        'validated_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => MembershipStatus::class,
            'joined_at' => 'datetime',
            'validated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
