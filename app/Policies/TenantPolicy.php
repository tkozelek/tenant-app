<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionTo('tenants.view_any')) {
            return true;
        }

        return false;
    }

    public function view(User $user, Tenant $tenant): bool
    {
        if ($user->hasPermissionTo('tenants.view_any')) {
            return true;
        }

        return $user->hasPermissionToOnTenant('tenant.access', $tenant->id);
    }

    public function create(User $user): bool
    {
        if ($user->hasPermissionTo('tenants.create')) {
            return true;
        }

        return false;
    }

    public function manageRoles(User $user, Tenant $tenant): bool
    {
        return $user->hasPermissionToOnTenant('tenant.users.manage', $tenant);
    }

    public function update(User $user, Tenant $tenant): bool
    {
        if ($user->hasPermissionTo('tenants.update')) {
            return true;
        }

        return $user->hasPermissionToOnTenant('tenant.settings', $tenant->id);
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        if ($tenant->owner_id === $user->id) {
            return true;
        }

        return false;
    }

    public function restore(User $user, Tenant $tenant): bool
    {
        return $this->delete($user, $tenant);
    }

    public function forceDelete(User $user, Tenant $tenant): bool
    {
        return $this->delete($user, $tenant);
    }

    public function manageTenant(User $user, Tenant $tenant): bool
    {
        return $this->update($user, $tenant);
    }

    public function assignRoles(User $user, Tenant $tenant): bool
    {
        if ($user->hasPermissionTo('tenant.users.manage')) {
            return true;
        }

        return $user->hasPermissionToOnTenant('tenant.users.manage', $tenant);
    }

    public function exportAny(User $user): bool
    {
        return $user->hasPermissionTo('tenants.export');
    }

    public function importAny(User $user): bool
    {
        return $user->hasPermissionTo('tenants.import');
    }

    public function changeOwner(User $user, Tenant $tenant): bool
    {
        if ($tenant->owner_id === $user->id) {
            return true;
        }

        return false;
    }
}
