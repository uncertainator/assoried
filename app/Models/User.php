<?php

namespace App\Models;

use App\Enums\AccountStatus;
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

    protected $fillable = ['name', 'email', 'password', 'password_setup_dismissed_at', 'consent_display_contact', 'onboarding_completed', 'account_status'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Effective role override set by the impersonation middleware. NOT persisted —
     * never assign to the `role` attribute, or Eloquent dirty-tracking would flush
     * the simulated role to the database on the next save().
     */
    public ?UserRole $impersonatedRole = null;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_setup_dismissed_at' => 'datetime',
            'consent_display_contact' => 'boolean',
            'onboarding_completed' => 'boolean',
            'role' => UserRole::class,
            'account_status' => AccountStatus::class,
        ];
    }

    public function needsPasswordSetup(): bool
    {
        return is_null($this->password) && is_null($this->password_setup_dismissed_at);
    }

    /**
     * Resolve the effective role for authorization. Returns the impersonated role
     * when a superadmin is endorsing a lower role, otherwise the real role.
     */
    public function effectiveRole(): UserRole
    {
        return $this->impersonatedRole ?? $this->role;
    }

    public function isAdmin(): bool
    {
        // Hierarchical: a superadmin inherits full admin access.
        return $this->effectiveRole() === UserRole::Admin
            || $this->effectiveRole() === UserRole::Superadmin;
    }

    public function isReferent(): bool
    {
        return $this->effectiveRole() === UserRole::Referent;
    }

    public function isAdherent(): bool
    {
        return $this->effectiveRole() === UserRole::Adherent;
    }

    /** Effective superadmin (false while impersonating a lower role). */
    public function isSuperadmin(): bool
    {
        return $this->effectiveRole() === UserRole::Superadmin;
    }

    public function isImpersonating(): bool
    {
        return $this->impersonatedRole !== null;
    }

    public function isPending(): bool
    {
        return $this->account_status === AccountStatus::Pending;
    }

    public function isActive(): bool
    {
        return $this->account_status === AccountStatus::Active;
    }

    public function isRejected(): bool
    {
        return $this->account_status === AccountStatus::Rejected;
    }

    public function isExcluded(): bool
    {
        return $this->account_status === AccountStatus::Excluded;
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

    public function scopePending($query)
    {
        return $query->where('account_status', AccountStatus::Pending);
    }
}
