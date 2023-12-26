<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(UrlGenerator $url): void
    {
        if (env('APP_ENV') !== 'local') {
            $url->forceScheme('https');
        }
    }
}
