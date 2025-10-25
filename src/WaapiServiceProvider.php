<?php

namespace OctopusTeam\Waapi;

use Illuminate\Support\ServiceProvider;
use OctopusTeam\Waapi\Console\Commands\RenewWebhookToken;

class WaapiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
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

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
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
    }
}
