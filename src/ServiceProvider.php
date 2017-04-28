<?php namespace Songshenzong\RequestLog;

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
            'Songshenzong\RequestLog\DataFormatter\DataFormatter',
            'Songshenzong\RequestLog\DataFormatter\DataFormatterInterface'
        );

        $this -> app -> singleton('songshenzong', function ($app) {
            $debugbar = new LaravelDebugbar($app);

            if ($app -> bound(SessionManager::class)) {
                $sessionManager = $app -> make(SessionManager::class);
                $httpDriver     = new SymfonyHttpDriver($sessionManager);
                $debugbar -> setHttpDriver($httpDriver);
            }

            return $debugbar;
        });

        $this -> app -> alias('songshenzong', 'Songshenzong\RequestLog\LaravelDebugbar');
    }


    /**
     * Check if is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this -> enabled === null) {
            $environments    = config('request-log.env', ['dev', 'local', 'production']);
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
        $configPath = __DIR__ . '/../config/request-log.php';
        $this -> publishes([$configPath => config_path('request-log.php')], 'config');


        if (!$this -> isEnabled()) {
            return;
        }


        app('Illuminate\Contracts\Http\Kernel') -> pushMiddleware('Songshenzong\RequestLog\Middleware');


        $routeConfig = [
            'namespace' => 'Songshenzong\RequestLog\Controllers',
            'prefix'    => config('request-log.route_prefix', 'request_logs'),
        ];

        app('router') -> group($routeConfig, function ($router) {
            $router -> get('', 'WebController@index');
            $router -> get('login', 'WebController@login');
            $router -> get('api/login', 'ApiController@login');
            $router -> group(['middleware' => 'Songshenzong\RequestLog\TokenMiddleware'], function ($router) {
                $router -> get('logs', 'ApiController@getList');
                $router -> get('logs/{id}', 'ApiController@getItem') -> where('id', '[0-9\.]+');
                $router -> get('destroy', 'ApiController@destroy');
                $router -> get('create', 'ApiController@createTable');
                $router -> get('collect/status', 'ApiController@getOrSetCollectStatus');
                $router -> get('table/status', 'ApiController@getTableStatus');
            });
        });

        if (app() -> runningInConsole() || app() -> environment('testing')) {
            return;
        }


        app('songshenzong') -> enable();
        app('songshenzong') -> boot();
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    // public function provides()
    // {
    //     return ['songshenzong', 'command.songshenzong.clear'];
    // }


    /**
     * Publish the config file
     *
     * @param  string $configPath
     */
    protected function publishConfig($configPath)
    {
        $this -> publishes([$configPath => config_path('request-log.php')], 'config');
    }
}
