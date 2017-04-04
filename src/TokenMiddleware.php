<?php namespace Songshenzong\Log;

use Error;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Songshenzong\Log\LaravelDebugbar;
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
     * Create a new middleware instance.
     *
     * @param  Container       $container
     * @param  LaravelDebugbar $debugbar
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
            $tokens = config('songshenzong.token');
            if (in_array($request -> token, $tokens)) {
                return $next($request);
            }
            return $this -> songshenzong -> json(403, 'Token invalid!');
        }
        return $this -> songshenzong -> json(403, 'No Token!');

    }

}
