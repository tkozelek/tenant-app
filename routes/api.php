<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Route;

Route::get('/tenants', function () {
    return Tenant::with('media')->get();
});
