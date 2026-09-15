<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\JWT\IdTokenVerifier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Only ID-token verification is needed (mobile OTP login), which only
        // requires the Firebase project ID — unlike Kreait's full Auth client,
        // this never needs a service account.
        $this->app->singleton(IdTokenVerifier::class, fn () => IdTokenVerifier::createWithProjectId(
            config('services.firebase.project_id')
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Authenticate::redirectUsing(fn () => route('login'));
    }
}
