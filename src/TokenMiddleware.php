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
     * The Songshenzong instance
     *
     * @var LaravelDebugbar
     */
    protected $songshenzong;


    /**
     * TokenMiddleware constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param \Songshenzong\RequestLog\LaravelDebugbar         $songshenzong
     */
    public function __construct(Container $container, LaravelDebugbar $songshenzong)
    {
        $this -> container    = $container;
        $this -> songshenzong = $songshenzong;
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
            return $this -> songshenzong -> json(403, $request -> token . ' is Invalid Token !');
        }
        return $this -> songshenzong -> json(403, 'No Token !');

    }

}
