<?php

namespace Songshenzong\Log\Support\Laravel;

use Songshenzong\Log\Songshenzong;
use Songshenzong\Log\DataSource\PhpDataSource;
use Songshenzong\Log\DataSource\LaravelDataSource;
use Songshenzong\Log\DataSource\EloquentDataSource;
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider {
	
	public function boot() {
		
		// Don't bother registering event listeners as we are not collecting data
		if ( ! $this -> app['songshenzong.support'] -> isCollectingData()) {
			return;
		}
		
		$this -> app['songshenzong.eloquent'] -> listenToEvents();
		
		// create the songshenzong instance so all data sources are initialized at this point
		$this -> app -> make('songshenzong');
		
		// Songshenzong is disabled, don't register the route
		if ( ! $this -> app['songshenzong.support'] -> isEnabled()) {
			return;
		}
		
		$this -> app['router'] -> group([
			                                'as'        => 'songshenzong::',
			                                'namespace' => 'Songshenzong\Log\Support\Laravel\Controllers',
			                                'prefix'    => 'songshenzong',
		                                ], function ($router) {
			$router -> get('', 'LogController@index');
			
			$router -> get('logs', 'LogController@getList');
			
			$router -> get('destroy', 'LogController@destroy');
			
			$router -> get('logs/{id}', 'LogController@getData') -> where('id', '[0-9\.]+');
		});
		
		
	}
	
	public function register() {
		
		$this -> publishes([__DIR__ . '/config/songshenzong.php' => config_path('songshenzong.php')]);
		
		$this -> app -> singleton('songshenzong.support', function ($app) {
			return new Support($app);
		});
		
		$this -> app -> singleton('songshenzong.laravel', function ($app) {
			return new LaravelDataSource($app);
		});
		
		$this -> app -> singleton('songshenzong.eloquent', function ($app) {
			return new EloquentDataSource($app['db'], $app['events']);
		});
		
		foreach ($this -> app['songshenzong.support'] -> getAdditionalDataSources() as $name => $callable) {
			$this -> app -> singleton($name, $callable);
		}
		
		$this -> app -> singleton('songshenzong', function ($app) {
			$songshenzong = new Songshenzong();
			
			$songshenzong -> addDataSource(new PhpDataSource()) -> addDataSource($app['songshenzong.laravel']);
			
			if ($app['songshenzong.support'] -> isCollectingDatabaseQueries()) {
				$songshenzong -> addDataSource($app['songshenzong.eloquent']);
			}
			
			foreach ($app['songshenzong.support'] -> getAdditionalDataSources() as $name => $callable) {
				$songshenzong -> addDataSource($app[$name]);
			}
			
			$songshenzong -> setStorage($app['songshenzong.support'] -> getStorage());
			
			return $songshenzong;
		});
		
		$this -> app['songshenzong.laravel'] -> listenToEvents();
		
		// set up aliases for all Songshenzong parts so they can be resolved by the IoC container
		$this -> app -> alias('songshenzong.support', 'Songshenzong\Support\Laravel\Support');
		$this -> app -> alias('songshenzong.laravel', 'Songshenzong\DataSource\LaravelDataSource');
		$this -> app -> alias('songshenzong.eloquent', 'Songshenzong\DataSource\EloquentDataSource');
		$this -> app -> alias('songshenzong', 'Songshenzong\Songshenzong');
		
		$this -> registerCommands();
		
		require __DIR__ . '/Helpers.php';
		
		
	}
	
	/**
	 * Register the artisan commands.
	 */
	public function registerCommands() {
		// Clean command
		$this -> app -> bind('command.songshenzong.clean', 'Songshenzong\Log\Support\Laravel\CleanCommand');
		
		$this -> commands('command.songshenzong.clean');
	}
	
	public function provides() {
		return ['songshenzong'];
	}
	
}
