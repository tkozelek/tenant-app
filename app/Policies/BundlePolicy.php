<?php

namespace App\Policies;

use App\Models\Bundle;
use App\Models\User;
use Filament\Facades\Filament;

class BundlePolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionTo('bundles.view_any')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.bundles.view_any', $tenant->id);
        }

        return false;
    }

    public function view(User $user, Bundle $bundle): bool
    {
        return $user->hasPermissionTo('bundles.view_any')
            || $user->hasPermissionToOnTenant('tenant.bundles.view_any', $bundle->tenant_id);
    }

    public function create(User $user): bool
    {
        if ($user->hasPermissionTo('bundles.create')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.bundles.create', $tenant->id);
        }

        return false;
    }

    public function update(User $user, Bundle $bundle): bool
    {
        return $user->hasPermissionTo('bundles.update')
            || $user->hasPermissionToOnTenant('tenant.bundles.update', $bundle->tenant_id);
    }

    public function delete(User $user, Bundle $bundle): bool
    {
        return $user->hasPermissionTo('bundles.delete')
            || $user->hasPermissionToOnTenant('tenant.bundles.delete', $bundle->tenant_id);
    }

    public function restore(User $user, Bundle $bundle): bool
    {
        return $this->delete($user, $bundle);
    }

    public function forceDelete(User $user, Bundle $bundle): bool
    {
        return $this->delete($user, $bundle);
    }
}
