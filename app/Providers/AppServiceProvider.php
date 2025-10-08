<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (env('REDIRECT_HTTPS')) {
            $this->app['request']->server->set('HTTPS', true);
        }

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (env('REDIRECT_HTTPS')) {
            URL::forceScheme('https');
        }
    }
}
