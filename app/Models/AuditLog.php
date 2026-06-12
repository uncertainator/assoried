<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public const TYPE_IMPERSONATION_START = 'impersonation_start';

    public const TYPE_IMPERSONATION_STOP = 'impersonation_stop';

    public const TYPE_ROLE_CHANGE = 'role_change';

    public $timestamps = false;

    protected $fillable = [
        'type', 'actor_id', 'target_user_id', 'old_role', 'new_role', 'meta', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Write an audit entry synchronously (no queue — host runs QUEUE_CONNECTION=sync).
     */
    public static function record(string $type, ?User $actor, array $attrs = []): self
    {
        return self::create(array_merge([
            'type' => $type,
            'actor_id' => $actor?->id,
            'created_at' => now(),
        ], $attrs));
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
