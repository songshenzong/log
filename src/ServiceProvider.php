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
     * True when enabled, false disabled an null for still unknown
     *
     * @var bool
     */
    protected $enabled = null;


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
     * Check if the Debugbar is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this -> enabled === null) {
            $this -> enabled = (boolean)config('debugbar.enabled');;
        }


        if ($this -> enabled === true) {
            $environments    = config('debugbar.env', ['dev', 'local', 'production']);
            $this -> enabled = in_array(env('APP_ENV'), $environments);
        }

        return $this -> enabled;
    }


    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        if (!$this -> isEnabled()) {
            return;
        }

        $routeConfig = [
            'namespace' => 'Songshenzong\Log\Controllers',
            'prefix'    => 'songshenzong',
        ];

        app('router') -> group($routeConfig, function ($router) {

            $router -> get('', 'LogController@index');

            $router -> get('logs', 'LogController@getList');

            $router -> get('logs/{id}', 'LogController@getData') -> where('id', '[0-9\.]+');

            $router -> get('destroy', 'LogController@destroy');

            $router -> get('drop', 'LogController@dropTable');

            $router -> get('create', 'LogController@createTable');

        });

        if (app() -> runningInConsole() || app() -> environment('testing')) {
            return;
        }


        app('debugbar') -> enable();
        app('debugbar') -> boot();


        app('Illuminate\Contracts\Http\Kernel') -> pushMiddleware('Songshenzong\Log\Middleware');
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
