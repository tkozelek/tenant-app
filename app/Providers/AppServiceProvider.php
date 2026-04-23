<?php

namespace App\Providers;

use App\Models\ApiToken;
use App\Models\User;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(ApiToken::class);

        Gate::define('viewPulse', function (User $user) {
            return in_array($user->email, config('app.pulse_emails', [
                'tommyside@centrum.sk',
            ]));
        });

        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi): void {
                $openApi->secure(SecurityScheme::http('bearer'));
            });

        //        Gate::define('viewApiDocs', fn (User $user) => $user->hasPermissionTo('tenant.view_api_docs'));

        RateLimiter::for('api', function (Request $request) {
            $token = $request->attributes->get('api_token');

            return Limit::perMinute(120)->by($token?->id ?: $request->ip());
        });

        RateLimiter::for('api-sensitive', function (Request $request) {
            $token = $request->attributes->get('api_token');

            return Limit::perMinute(20)->by($token?->id ?: $request->ip());
        });
    }
}
