<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\User;
use Filament\Facades\Filament;

class TenantProductPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionTo('catalog.manage') || $user->hasPermissionTo('products.view_any')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.products.view_any', $tenant->id);
        }

        return false;
    }

    public function view(User $user, TenantProduct $tenantProduct): bool
    {
        return $user->hasPermissionTo('catalog.manage')
            || $user->hasPermissionTo('products.view_any')
            || $user->hasPermissionToOnTenant('tenant.products.view_any', $tenantProduct->tenant_id);
    }

    public function create(User $user): bool
    {
        if ($user->hasPermissionTo('catalog.manage') || $user->hasPermissionTo('products.create')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.products.create', $tenant->id);
        }

        return false;
    }

    public function createForTenant(User $user, Tenant $tenant): bool
    {
        return $user->hasPermissionTo('catalog.manage')
            || $user->hasPermissionTo('products.create')
            || $user->hasPermissionToOnTenant('tenant.products.create', $tenant->id);
    }

    public function update(User $user, TenantProduct $tenantProduct): bool
    {
        return $user->hasPermissionTo('catalog.manage')
            || $user->hasPermissionTo('products.update')
            || $user->hasPermissionToOnTenant('tenant.products.update', $tenantProduct->tenant_id);
    }

    public function delete(User $user, TenantProduct $tenantProduct): bool
    {
        return $user->hasPermissionTo('catalog.manage')
            || $user->hasPermissionTo('products.delete')
            || $user->hasPermissionToOnTenant('tenant.products.delete', $tenantProduct->tenant_id);
    }

    public function exportAny(User $user): bool
    {
        return $user->hasPermissionTo('products.export');
    }

    public function importAny(User $user): bool
    {
        return $user->hasPermissionTo('products.import');
    }
}
