<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionToOnFilamentTenant('tenant.users.manage')) {
            return true;
        }

        return $user->hasAnyPermission('roles.view_any');
    }

    public function create(User $user): bool
    {
        if ($user->hasPermissionToOnFilamentTenant('tenant.users.manage')) {
            return true;
        }

        return $user->hasAnyPermission('roles.create');
    }

    public function update(User $user, Role $role): bool
    {
        if ($user->hasPermissionToOnFilamentTenant('tenant.users.manage')) {
            return true;
        }

        return $user->hasAnyPermission('roles.update');
    }

    public function delete(User $user, Role $role): bool
    {
        if ($user->hasPermissionToOnFilamentTenant('tenant.users.manage')) {
            return true;
        }

        return $user->hasAnyPermission('roles.delete');
    }

    public function exportAny(User $user): bool
    {
        return $user->hasPermissionTo('roles.export');
    }

    public function importAny(User $user): bool
    {
        return $user->hasPermissionTo('roles.import');
    }
}
