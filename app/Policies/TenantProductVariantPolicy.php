<?php

namespace App\Policies;

use App\Models\TenantProductVariant;
use App\Models\User;
use Filament\Facades\Filament;

class TenantProductVariantPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionTo('catalog.manage')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.variants.view_any', $tenant->id);
        }

        return false;
    }

    public function view(User $user, TenantProductVariant $variant): bool
    {
        return $user->hasPermissionTo('catalog.manage')
            || $user->hasPermissionToOnTenant('tenant.variants.view_any', $variant->product->tenant_id);
    }

    public function create(User $user): bool
    {
        if ($user->hasPermissionTo('catalog.manage')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.variants.create', $tenant->id);
        }

        return false;
    }

    public function update(User $user, TenantProductVariant $variant): bool
    {
        return $user->hasPermissionTo('catalog.manage')
            || $user->hasPermissionToOnTenant('tenant.variants.update', $variant->product->tenant_id);
    }

    public function delete(User $user, TenantProductVariant $variant): bool
    {
        return $user->hasPermissionTo('catalog.manage')
            || $user->hasPermissionToOnTenant('tenant.variants.delete', $variant->product->tenant_id);
    }
}
