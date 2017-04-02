<?php

namespace Songshenzong\Support\Laravel;

use Closure;
use Exception;
use Illuminate\Foundation\Application;

class Middleware {
	
	/**
	 * The Laravel Application
	 *
	 * @var Application
	 */
	protected $app;
	
	/**
	 * Middleware constructor.
	 *
	 * @param \Illuminate\Foundation\Application $app
	 */
	public function __construct(Application $app) {
		$this -> app = $app;
	}
	
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		
		$this -> app['config'] -> set('songshenzong::config.middleware', TRUE);
		
		try {
			$response = $next($request);
		} catch (Exception $e) {
			$this -> app['Illuminate\Contracts\Debug\ExceptionHandler'] -> report($e);
			$response = $this -> app['Illuminate\Contracts\Debug\ExceptionHandler'] -> render($request, $e);
		}
		
		return $this -> app['songshenzong.support'] -> process($request, $response);
	}
	
}
