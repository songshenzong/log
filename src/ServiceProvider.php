<?php namespace Songshenzong\Log;

use Illuminate\Session\SessionManager;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this -> app -> alias(
            'Songshenzong\Log\DataFormatter\DataFormatter',
            'Songshenzong\Log\DataFormatter\DataFormatterInterface'
        );

        $this -> app -> singleton('debugbar', function ($app) {
            $debugbar = new LaravelDebugbar($app);

            if ($app -> bound(SessionManager::class)) {
                $sessionManager = $app -> make(SessionManager::class);
                $httpDriver     = new SymfonyHttpDriver($sessionManager);
                $debugbar -> setHttpDriver($httpDriver);
            }

            return $debugbar;
        }
        );

        $this -> app -> alias('debugbar', 'Songshenzong\Log\LaravelDebugbar');

        $this -> app -> singleton('command.debugbar.clear',
            function ($app) {
                return new Console\ClearCommand($app['debugbar']);
            }
        );

        $this -> commands(['command.debugbar.clear']);
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        $app = $this -> app;


        // If enabled is null, set from the app.debug value
        $enabled = $this -> app['config'] -> get('debugbar.enabled');

        if (is_null($enabled)) {
            $enabled = $this -> app['config'] -> get('app.debug');
        }

        if (!$enabled) {
            return;
        }

        $routeConfig = [
            'namespace' => 'Songshenzong\Log\Controllers',
            'prefix'    => 'songshenzong',
        ];

        $this -> app['router'] -> group($routeConfig, function ($router) {

            $router -> get('', 'LogController@index');

            $router -> get('logs', 'LogController@getList');

            $router -> get('destroy', 'LogController@destroy');

            $router -> get('logs/{id}', 'LogController@getData') -> where('id', '[0-9\.]+');


        });

        if ($app -> runningInConsole() || $app -> environment('testing')) {
            return;
        }

        /** @var LaravelDebugbar $debugbar */
        $debugbar = $this -> app['debugbar'];
        $debugbar -> enable();
        $debugbar -> boot();

        $this -> registerMiddleware('Songshenzong\Log\Middleware');
    }


    /**
     * Register the Debugbar Middleware
     *
     * @param  string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        $kernel = $this -> app['Illuminate\Contracts\Http\Kernel'];
        $kernel -> pushMiddleware($middleware);
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['debugbar', 'command.debugbar.clear'];
    }
}
