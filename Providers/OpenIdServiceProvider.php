<?php

namespace Modules\OpenId\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Modules\OpenId\Guards\CustomSessionGuard;
use Modules\OpenId\Guards\CustomTokenGuard;
use Modules\OpenId\Services\Api;
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
        $this->registerViewComposers();

        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', config('openid.namespace'));
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', config('openid.namespace'));

        $this->app->singleton('openid', function ($app) {
            return new Client();
        });

        $this->app->singleton('oauth2', function ($app) {
            return new Api();
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
        $this->registerBladeDirectives();
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

    /**
     * Register Guard Session
     *
     * @return void
     */
    protected function registerGuard()
    {
        \Auth::extend('openid', function ($app, $name, array $config) {
            return new CustomSessionGuard($app['session.store']);
        });

        \Auth::extend('basic-api', function ($app, $name, array $config) {
            return new CustomTokenGuard();
        });
    }

    /**
     * Register ViewComposers
     *
     * @return void
     */
    protected function registerViewComposers()
    {
        View::composer(
            'openid::menu', 'Modules\OpenId\Http\ViewComposers\UserSystemsComposer'
        );
    }

    /**
     * Register Blade Directives
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
        Blade::directive('openidComponents', function () {
            return "<?php echo view('openid::menu')->render(); ?>";
        });

        Blade::directive('login', function ($route) {
            if(\Auth::check()) return "<?php echo route($route); ?>";

            return "<?php echo config('openid.server') . '/login?' . http_build_query(['continue' => route($route)]); ?>";
        });
    }
}
