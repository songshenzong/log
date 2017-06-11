<?php namespace Songshenzong\Log\Controllers;

use Songshenzong\Log\LaravelDebugbar;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

if (class_exists('Illuminate\Routing\Controller')) {
    /**
     * Class BaseController
     *
     * @package Songshenzong\Log\Controllers
     */
    class BaseController extends Controller
    {
        /**
         * @var LaravelDebugbar
         */
        protected $debugbar;

        /**
         * BaseController constructor.
         *
         * @param Request         $request
         * @param LaravelDebugbar $debugbar
         */
        public function __construct(Request $request, LaravelDebugbar $debugbar)
        {
            $this -> debugbar = $debugbar;

            if ($request -> hasSession()) {
                $request -> session() -> reflash();
            }
        }
    }
} else {
    /**
     * Class BaseController
     *
     * @package Songshenzong\Log\Controllers
     */
    class BaseController
    {
        /**
         * @var LaravelDebugbar
         */
        protected $debugbar;

        /**
         * BaseController constructor.
         *
         * @param Request         $request
         * @param LaravelDebugbar $debugbar
         */
        public function __construct(Request $request, LaravelDebugbar $debugbar)
        {
            $this -> debugbar = $debugbar;

            if ($request -> hasSession()) {
                $request -> session() -> reflash();
            }
        }
    }
}
