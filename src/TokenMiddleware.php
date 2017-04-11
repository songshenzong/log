<?php namespace Songshenzong\RequestLog;

use Error;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Songshenzong\RequestLog\LaravelDebugbar;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class TokenMiddleware
{
    /**
     * The App container
     *
     * @var Container
     */
    protected $container;

    /**
     * The instance
     *
     * @var LaravelDebugbar
     */
    protected $instance;


    /**
     * TokenMiddleware constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param \Songshenzong\RequestLog\LaravelDebugbar  $instance
     */
    public function __construct(Container $container, LaravelDebugbar $instance)
    {
        $this -> container = $container;
        $this -> instance  = $instance;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (isset($request -> token)) {
            $tokens = config('request-log.token', ['request-log']);
            if (in_array($request -> token, $tokens)) {
                return $next($request);
            }
            return $this -> instance -> json(403, $request -> token . ' is Invalid Token !');
        }
        return $this -> instance -> json(403, 'No Token !');

    }

}
