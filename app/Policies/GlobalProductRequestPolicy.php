<?php

namespace App\Policies;

use App\Models\GlobalProductRequest;
use App\Models\User;
use Filament\Facades\Filament;

class GlobalProductRequestPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionTo('catalog.manage')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.requests.view_any', $tenant->id);
        }

        return false;
    }

    public function view(User $user, GlobalProductRequest $globalProductRequest): bool
    {
        return $user->hasPermissionTo('catalog.manage')
            || $user->hasPermissionToOnTenant('tenant.requests.view_any', $globalProductRequest->tenant_id);
    }

    public function create(User $user): bool
    {
        if ($user->hasPermissionTo('catalog.manage')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.requests.create', $tenant->id);
        }

        return false;
    }

    public function update(User $user, GlobalProductRequest $globalProductRequest): bool
    {
        return $user->hasPermissionTo('catalog.manage')
            || $user->hasPermissionToOnTenant('tenant.requests.update', $globalProductRequest->tenant_id);
    }

    public function delete(User $user, GlobalProductRequest $globalProductRequest): bool
    {
        return $user->hasPermissionTo('catalog.manage');
    }
}
