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

        $this -> app -> singleton('RequestLog', function ($app) {
            $debugbar = new LaravelDebugbar($app);

            if ($app -> bound(SessionManager::class)) {
                $sessionManager = $app -> make(SessionManager::class);
                $httpDriver     = new SymfonyHttpDriver($sessionManager);
                $debugbar -> setHttpDriver($httpDriver);
            }

            return $debugbar;
        }
        );

        $this -> app -> alias('RequestLog', 'Songshenzong\RequestLog\Facade');


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

            $router -> group(['middleware' => 'Songshenzong\RequestLog\TokenMiddleware'], function ($router) {

                $router -> get('logs', 'ApiController@getList');

                $router -> get('logs/{id}', 'ApiController@getData') -> where('id', '[0-9\.]+');

                $router -> get('destroy', 'ApiController@destroy');

                $router -> get('create', 'ApiController@createTable');

            });


        });

        if (app() -> runningInConsole() || app() -> environment('testing')) {
            return;
        }


        app('RequestLog') -> enable();
        app('RequestLog') -> boot();

    }


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
