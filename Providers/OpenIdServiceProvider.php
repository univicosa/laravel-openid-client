<?php

namespace Modules\OpenId\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\OpenId\Guards\CustomSessionGuard;
use Modules\OpenId\Guards\CustomTokenGuard;
use Modules\OpenId\Services\Client;

class OpenIdServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = FALSE;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        $this->app->singleton('openid', function ($app) {
            return new Client();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGuard();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('openid.php'),
        ], 'openid-config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'openid'
        );
    }

    protected function registerGuard()
    {
        \Auth::extend('openid', function ($app, $name, array $config) {
            return new CustomSessionGuard($app['session.store']);
        });

        \Auth::extend('basic-api', function ($app, $name, array $config) {
            return new CustomTokenGuard();
        });
    }
}
