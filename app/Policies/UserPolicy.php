<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionToOnFilamentTenant('tenant.users.view')) {
            return true;
        }

        return $user->hasPermissionTo('users.view_any');
    }

    public function view(User $user, User $model): bool
    {
        if ($user->hasPermissionToOnFilamentTenant('tenant.users.view')) {
            return true;
        }

        if ($user->hasPermissionTo('users.view_any')) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create');
    }

    public function update(User $user, User $model): bool
    {
        if ($user->hasPermissionToOnFilamentTenant('tenant.users.manage')) {
            return true;
        }

        if ($user->hasPermissionTo('users.update')) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->hasPermissionToOnFilamentTenant('tenant.users.manage')) {
            return true;
        }

        if ($user->hasPermissionTo('users.delete')) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function exportAny(User $user): bool
    {
        return $user->hasPermissionTo('users.export');
    }

    public function importAny(User $user): bool
    {
        return $user->hasPermissionTo('users.import');
    }
}
