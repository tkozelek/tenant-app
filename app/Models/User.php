<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName, HasTenants
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
            get: fn () => $this->first_name.' '.$this->last_name,
        );
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

    public function isOwnerOfTenant(int $tenantId): bool
    {
        return $this->ownedTenants()->where('id', $tenantId)->exists();
    }

    public function hasPermissionToOnTenant(string $permission, $tenant): bool
    {
        $tenantId = is_numeric($tenant) ? $tenant : $tenant->id;

        if ($this->isOwnerOfTenant($tenantId)) {
            return true;
        }

        $originalTeamId = getPermissionsTeamId();

        setPermissionsTeamId($tenantId);

        $this->unsetRelation('roles', 'permissions');

        $result = $this->hasPermissionTo($permission);

        setPermissionsTeamId($originalTeamId);

        return $result;
    }

    public function ownedTenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'owner_id');
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(
            Tenant::class,
            config('permission.table_names.model_has_roles'),
            'model_id',
            'tenant_id'
        )
            ->withPivot('role_id', 'model_type');
    }

    public function shops(): BelongsToMany
    {
        return $this->tenants();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->can('platform.access');
        }

        if ($panel->getId() === 'tenant') {
            return $this->getTenants($panel)->isNotEmpty();
        }

        return false;
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return $this->ownedTenants->merge($this->tenants)->unique('id');
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $tenant->owner_id === $this->id
            || $this->tenants()->whereKey($tenant)->exists();
    }
}
