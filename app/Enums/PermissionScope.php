<?php

namespace App\Enums;

enum PermissionScope: string
{
    case App = 'app';
    case Tenant = 'tenant';
}
