<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles {
        hasPermissionTo as spatieHasPermissionTo;
        checkPermissionTo as spatieCheckPermissionTo;
    }

    private ?array $revokedPermissionNameCache = null;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function revokedPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permission_revocations', 'user_id', 'permission_id');
    }

    private function revokedPermissionNames(): array
    {
        if ($this->revokedPermissionNameCache !== null) return $this->revokedPermissionNameCache;

        $this->revokedPermissionNameCache = $this->revokedPermissions()
            ->pluck('name')
            ->map(fn ($n) => (string) $n)
            ->values()
            ->all();

        return $this->revokedPermissionNameCache;
    }

    public function effectivePermissionNames(): array
    {
        $all = $this->getAllPermissions()->pluck('name')->map(fn ($n) => (string) $n)->all();
        $revoked = $this->revokedPermissionNames();

        return array_values(array_diff($all, $revoked));
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        $permissionName = is_string($permission) ? $permission : ($permission?->name ?? null);
        if ($permissionName && in_array($permissionName, $this->revokedPermissionNames(), true)) return false;

        return $this->spatieHasPermissionTo($permission, $guardName);
    }

    public function checkPermissionTo($permission, $guardName = null): bool
    {
        $permissionName = is_string($permission) ? $permission : ($permission?->name ?? null);
        if ($permissionName && in_array($permissionName, $this->revokedPermissionNames(), true)) return false;

        return $this->spatieCheckPermissionTo($permission, $guardName);
    }
}
