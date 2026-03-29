<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Tenant::class => \App\Policies\TenantPolicy::class,
        \App\Models\TenantProduct::class => \App\Policies\TenantProductPolicy::class,
        \App\Models\TenantProductVariant::class => \App\Policies\TenantProductVariantPolicy::class,
        \App\Models\GlobalProduct::class => \App\Policies\GlobalProductPolicy::class,
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
        \App\Models\GlobalProductRequest::class => \App\Policies\GlobalProductRequestPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Attribute::class => \App\Policies\AttributePolicy::class,
        \App\Models\AttributeValue::class => \App\Policies\AttributeValuePolicy::class,
        \App\Models\Bundle::class => \App\Policies\BundlePolicy::class,
        \App\Models\Activity::class => \App\Policies\ActivityLogPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
