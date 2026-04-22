<?php

namespace App\Models;

use App\Enums\PermissionScope;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected function casts(): array
    {
        return [
            'scope' => PermissionScope::class,
        ];
    }
}
