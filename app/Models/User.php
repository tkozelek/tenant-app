<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
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

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }

    public function ownedTenants()
    {
        return $this->hasMany(Tenant::class, 'owner_id');
    }

    /**
     * Check if the user belongs to a specific tenant (team).
     */
    public function belongsToTeam($tenant): bool
    {
        if ($tenant instanceof Tenant && $this->id === $tenant->owner_id) {
            return true;
        }

        return DB::table(config('permission.table_names.model_has_roles'))
            ->where('model_id', $this->id)
            ->where('model_type', self::class)
            ->where('tenant_id', $tenant->id ?? $tenant)
            ->exists();
    }

    public function hasPermissionToOnTenant(string $permission, Tenant $tenant): bool
    {
        $currentTeamId = getPermissionsTeamId();

        setPermissionsTeamId($tenant->id);

        try {
            return $this->hasPermissionTo($permission);
        } catch (\Exception $e) {
            return false;
        } finally {
            setPermissionsTeamId($currentTeamId);
        }
    }

    public function tenants(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Tenant::class,
            config('permission.table_names.model_has_roles'),
            'model_id',
            'tenant_id'
        )
            ->withPivot('role_id');
    }

    public function shops(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->tenants();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->can('platform.access');
        }

        if ($panel->getId() === 'tenant') {
            return $this->can('store.access');
        }

        return false;
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
