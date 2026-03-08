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

        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tenant $tenant): bool
    {
        if ($user->hasPermissionTo('tenants.view_any')) {
            return true;
        }

        if ($tenant->owner_id === $user->id) {
            return true;
        }

        return $user->hasPermissionToOnTenant('store.access', $tenant->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasPermissionTo('tenants.create')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tenant $tenant): bool
    {
        if ($user->hasPermissionTo('tenants.update')) {
            return true;
        }

        if ($tenant->owner_id === $user->id) {
            return true;
        }

        return $user->hasPermissionToOnTenant('store.settings', $tenant->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tenant $tenant): bool
    {
        if ($tenant->owner_id === $user->id) {
            return true;
        }

        if ($user->hasPermissionTo('tenants.delete')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tenant $tenant): bool
    {
        return $this->delete($user, $tenant);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tenant $tenant): bool
    {
        return $this->delete($user, $tenant);
    }

    /**
     * Determine whether the user can manage the tenant (general gate).
     */
    public function manageTenant(User $user, Tenant $tenant): bool
    {
        return $this->update($user, $tenant);
    }

    /**
     * Determine whether the user can assign roles in the tenant.
     */
    public function assignRoles(User $user, Tenant $tenant): bool
    {
        if ($tenant->owner_id === $user->id) {
            return true;
        }

        if ($user->hasPermissionTo('users.manage')) {
            return true;
        }

        return $user->hasPermissionToOnTenant('store.users.manage', $tenant);
    }

    /**
     * Determine whether the user can export any models.
     */
    public function exportAny(User $user): bool
    {
        return $user->hasPermissionTo('tenants.export');
    }

    /**
     * Determine whether the user can import any models.
     */
    public function importAny(User $user): bool
    {
        return $user->hasPermissionTo('tenants.import');
    }
}
