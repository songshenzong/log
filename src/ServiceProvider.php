<?php namespace Songshenzong\Log;

use Illuminate\Session\SessionManager;
use Illuminate\Contracts\Http\Kernel;

/**
 * Class ServiceProvider
 *
 * @package Songshenzong\Log
 */
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
        $this->app->alias(
            DataFormatter\DataFormatter::class,
            DataFormatter\DataFormatterInterface::class
        );

        $this->app->singleton('songshenzongLog', function ($app) {
            $debugbar = new LaravelDebugbar($app);

            if ($app->bound(SessionManager::class)) {
                $sessionManager = $app->make(SessionManager::class);
                $httpDriver     = new SymfonyHttpDriver($sessionManager);
                $debugbar->setHttpDriver($httpDriver);
            }

            return $debugbar;
        });

        $this->app->alias('songshenzongLog', LaravelDebugbar::class);
    }


    /**
     * Check if is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this->enabled === null) {
            $environments  = config('songshenzong-log.env', ['dev', 'local', 'production']);
            $this->enabled = in_array(env('APP_ENV'), $environments, true);
        }

        return $this->enabled;
    }


    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/songshenzong-log.php';
        $this->publishes([$configPath => config_path('songshenzong-log.php')], 'config');


        if (!$this->isEnabled()) {
            return;
        }


        $kernel = $this->app->make(Kernel::class);
        $kernel->prependMiddleware(Middleware::class);


        $routeConfig = [
            'namespace' => 'Songshenzong\Log\Controllers',
            'prefix'    => config('songshenzong-log.route_prefix', 'songshenzong/log'),
        ];

        app('router')->group($routeConfig, function ($router) {
            $router->get('', 'WebController@index');
            $router->get('login', 'WebController@login');
            $router->get('api/login', 'ApiController@login');
            $router->group(['middleware' => TokenMiddleware::class], function ($router) {
                $router->get('logs', 'ApiController@getList');
                $router->get('logs/{id}', 'ApiController@getItem')->where('id', '[0-9\.]+');
                $router->get('destroy', 'ApiController@destroy');
                $router->get('create', 'ApiController@createTable');
                $router->get('collect/status', 'ApiController@getOrSetCollectStatus');
                $router->get('table/status', 'ApiController@getTableStatus');
            });
        });

        if (app()->runningInConsole() || app()->environment('testing')) {
            return;
        }


        app('songshenzongLog')->enable();
        app('songshenzongLog')->boot();
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
        $this->publishes([$configPath => config_path('songshenzong-log.php')], 'config');
    }
}
