<?php

namespace App\Policies;

use App\Models\ApiToken;
use App\Models\User;
use Filament\Facades\Filament;

class ApiTokenPolicy
{
    public function viewAny(User $user): bool
    {
        if ($this->canEditToken($user)) {
            return true;
        }

        return $user->hasPermissionTo('api_tokens.view_any');
    }

    public function view(User $user, ApiToken $apiToken): bool
    {
        if ($this->canEditToken($user)) {
            return true;
        }

        return $user->hasPermissionTo('api_tokens.view_any');
    }

    public function create(User $user): bool
    {
        if ($this->canEditToken($user)) {
            return true;
        }

        return $user->hasPermissionTo('api_tokens.create');
    }

    public function delete(User $user, ApiToken $apiToken): bool
    {
        if ($this->canEditToken($user)) {
            return true;
        }

        return $user->hasPermissionTo('api_tokens.delete');
    }

    private function canEditToken(User $user): bool
    {
        $tenant = Filament::getTenant();

        return $user->hasPermissionToOnTenant('tenant.api_tokens.manage', $tenant);
    }
}
