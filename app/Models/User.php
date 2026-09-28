<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_STAFF = 'staff';
    public const ROLE_USER = 'user';

    public const ROLES = [
        self::ROLE_ADMIN => 'Administrator',
        self::ROLE_MANAGER => 'Staff Manager',
        self::ROLE_STAFF => 'Mortuary Staff',
        self::ROLE_USER => 'Family / Client',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function deceaseds(): HasMany
    {
        return $this->hasMany(Deceased::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Deceased a family / client unlocked with a verification key.
     */
    public function verifiedDeceased(): BelongsToMany
    {
        return $this->belongsToMany(Deceased::class)->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    /**
     * Family / client accounts: they only see deceased they verified or paid for.
     */
    public function isClient(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Admins and staff managers can supervise the whole mortuary.
     */
    public function canSupervise(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    /**
     * Deceased records this user is allowed to pick in forms (payments, faire-part).
     */
    public function visibleDeceased(): Builder
    {
        if (! $this->isClient()) {
            return Deceased::query();
        }

        return Deceased::query()->where(function (Builder $query) {
            $query->whereHas('verifiedBy', fn (Builder $users) => $users->whereKey($this->id))
                ->orWhereHas('payments', fn (Builder $payments) => $payments->where('user_id', $this->id));
        });
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst((string) $this->role);
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'admin.dashboard',
            self::ROLE_MANAGER => 'manager.dashboard',
            self::ROLE_STAFF => 'staff.dashboard',
            default => 'family.dashboard',
        };
    }
}
