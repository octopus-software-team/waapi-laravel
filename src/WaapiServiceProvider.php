<?php

namespace Octopus\Waapi;

use Illuminate\Support\ServiceProvider;

class WaapiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/waapi.php',
            'waapi'
        );

        $this->app->singleton(WaapiService::class, function () {
            return new WaapiService();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/waapi.php' => config_path('waapi.php'),
        ], 'config');
    }
}
