<?php

namespace OctopusTeam\Waapi;

use Illuminate\Support\ServiceProvider;

class WaapiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/waapi.php',
            'waapi'
        );

        $this->app->singleton(Waapi::class, function () {
            return new Waapi();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/waapi.php' => config_path('waapi.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                RenewWebhookToken::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

    }
}
