<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasUuid, SoftDeletes, HasFactory, Notifiable;
    use HasRoles {
        hasRole as spatieHasRole;
        hasAnyRole as spatieHasAnyRole;
    }

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'status',
        'suspended_until',
        'last_login_at',
        'last_login_ip',
        'user_agent',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'suspended_until'   => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Check if the user can authenticate (not suspended, active).
     */
    public function canAuthenticate(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && ($this->suspended_until === null || $this->suspended_until->isPast());
    }

    /**
     * Record login metadata.
     */
    public function recordLogin(string $ip, string $userAgent): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
            'user_agent'    => $userAgent,
        ]);
    }

    /**
     * Get the staff record associated with the user.
     */
    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    /**
     * Get the student record associated with the user.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the login history records for the user.
     */
    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include suspended users.
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    /**
     * Scope a query to search users by first name, last name, or email.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('first_name', 'like', '%' . $search . '%')
                     ->orWhere('last_name', 'like', '%' . $search . '%')
                     ->orWhere('email', 'like', '%' . $search . '%');
    }

    /**
     * Map slugs to DB role names.
     */
    protected static array $roleMap = [
        'admin'               => 'Super Admin',
        'head-teacher'        => 'Head Teacher',
        'teacher'             => 'Teacher',
        'boarding-officer'    => 'Boarding Officer',
        'warden-matron'       => 'Warden/Matron',
        'bursar'              => 'Bursar',
        'accountant'          => 'Accountant',
        'procurement-officer' => 'Store/Procurement Officer',
        'auditor'             => 'Auditor',
        'student'             => 'Student',
    ];

    /**
     * Map DB role names to slugs.
     */
    protected static array $slugMap = [
        'Super Admin'               => 'admin',
        'Head Teacher'              => 'head-teacher',
        'Teacher'                   => 'teacher',
        'Boarding Officer'          => 'boarding-officer',
        'Warden/Matron'             => 'warden-matron',
        'Bursar'                    => 'bursar',
        'Accountant'                => 'accountant',
        'Store/Procurement Officer' => 'procurement-officer',
        'Auditor'                   => 'auditor',
        'Student'                   => 'student',
    ];

    /**
     * Get the mapped DB role name from a slug.
     */
    public static function mapRoleName($role)
    {
        if (is_string($role)) {
            return self::$roleMap[strtolower($role)] ?? $role;
        }
        return $role;
    }

    /**
     * Get the slug version of the user's first role.
     */
    public function getRoleSlug(): string
    {
        $roleName = $this->getRoleNames()->first();
        return self::$slugMap[$roleName] ?? 'student';
    }

    /**
     * Get the user's primary role with name and slug properties.
     */
    public function getRoleAttribute()
    {
        $roleName = $this->getRoleNames()->first();
        if (!$roleName) {
            return null;
        }

        return (object) [
            'name' => $roleName,
            'slug' => self::$slugMap[$roleName] ?? 'student',
        ];
    }

    /**
     * Override Spatie hasRole to normalize roles.
     */
    public function hasRole($role, $guard = null): bool
    {
        if (is_string($role)) {
            $role = self::mapRoleName($role);
        } elseif (is_array($role)) {
            $role = array_map([self::class, 'mapRoleName'], $role);
        } elseif ($role instanceof \Illuminate\Support\Collection) {
            $role = $role->map(fn($r) => is_string($r) ? self::mapRoleName($r) : $r);
        }
        return $this->spatieHasRole($role, $guard);
    }

    /**
     * Override Spatie hasAnyRole to normalize roles.
     */
    public function hasAnyRole(...$roles): bool
    {
        $mappedRoles = [];
        $roles = is_array($roles[0] ?? null) ? $roles[0] : $roles;
        
        foreach ($roles as $role) {
            if (is_string($role)) {
                $mappedRoles[] = self::mapRoleName($role);
            } elseif (is_array($role)) {
                $mappedRoles = array_merge($mappedRoles, array_map([self::class, 'mapRoleName'], $role));
            } elseif ($role instanceof \Illuminate\Support\Collection) {
                $mappedRoles = array_merge($mappedRoles, $role->map(fn($r) => is_string($r) ? self::mapRoleName($r) : $r)->toArray());
            } else {
                $mappedRoles[] = $role;
            }
        }
        return $this->spatieHasAnyRole($mappedRoles);
    }
}

