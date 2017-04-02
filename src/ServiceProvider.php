<?php namespace Songshenzong\Log;

use Illuminate\Routing\Router;
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
        $configPath = __DIR__ . '/../config/debugbar.php';
        $this -> mergeConfigFrom($configPath, 'debugbar');

        $this -> app -> alias(
            'DebugBar\DataFormatter\DataFormatter',
            'DebugBar\DataFormatter\DataFormatterInterface'
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


    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $app = $this -> app;

        $configPath = __DIR__ . '/../config/debugbar.php';
        $this -> publishes([$configPath => $this -> getConfigPath()], 'config');

        // If enabled is null, set from the app.debug value
        $enabled = $this -> app['config'] -> get('debugbar.enabled');

        if (is_null($enabled)) {
            $enabled = $this -> checkAppDebug();
        }

        if (!$enabled) {
            return;
        }

        $routeConfig = [
            'as'        => 'songshenzong::',
            'namespace' => 'Songshenzong\Log\Controllers',
            'prefix'    => $this -> app['config'] -> get('debugbar.route_prefix'),
        ];

        $this -> getRouter() -> group($routeConfig, function ($router) {
            $router -> get('', 'LogController@index');

            $router -> get('logs', 'LogController@getList');

            $router -> get('destroy', 'LogController@destroy');

            $router -> get('logs/{id}', 'LogController@getData');


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
     * Get the active router.
     *
     * @return Router
     */
    protected function getRouter()
    {
        return $this -> app['router'];
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return config_path('debugbar.php');
    }

    /**
     * Publish the config file
     *
     * @param  string $configPath
     */
    protected function publishConfig($configPath)
    {
        $this -> publishes([$configPath => config_path('debugbar.php')], 'config');
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
     * Check the App Debug status
     */
    protected function checkAppDebug()
    {
        return $this -> app['config'] -> get('app.debug');
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
