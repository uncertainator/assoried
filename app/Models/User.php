<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'password_setup_dismissed_at', 'consent_display_contact'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_setup_dismissed_at' => 'datetime',
            'consent_display_contact' => 'boolean',
            'role' => UserRole::class,
        ];
    }

    public function needsPasswordSetup(): bool
    {
        return is_null($this->password) && is_null($this->password_setup_dismissed_at);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isReferent(): bool
    {
        return $this->role === UserRole::Referent;
    }

    public function isAdherent(): bool
    {
        return $this->role === UserRole::Adherent;
    }

    public function circles(): BelongsToMany
    {
        return $this->belongsToMany(Circle::class)->withPivot('joined_at');
    }

    public function assignedCircle(): HasOne
    {
        return $this->hasOne(Circle::class, 'referent_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(CircleMembership::class);
    }

    public function polls(): HasMany
    {
        return $this->hasMany(Poll::class, 'created_by');
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', UserRole::Admin);
    }
}
