<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FirebaseAuth::class, function () {
            $factory = new Factory;

            if ($credentials = config('services.firebase.credentials')) {
                $factory = $factory->withServiceAccount($credentials);
            }

            if ($projectId = config('services.firebase.project_id')) {
                $factory = $factory->withProjectId($projectId);
            }

            return $factory->createAuth();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Authenticate::redirectUsing(fn () => route('login'));
    }
}
