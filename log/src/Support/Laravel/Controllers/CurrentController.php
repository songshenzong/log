<?php

namespace Songshenzong\Support\Laravel\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Controller;

class CurrentController extends Controller {
	
	/**
	 * @var \Illuminate\Contracts\Foundation\Application
	 */
	public $app;
	
	/**
	 * CurrentController constructor.
	 *
	 * @param \Illuminate\Contracts\Foundation\Application $app
	 */
	public function __construct(Application $app) {
		$this -> app = $app;
	}
	
	/**
	 * @param null $id
	 * @param null $last
	 *
	 * @return mixed
	 */
	public function getData($id = NULL, $last = NULL) {
		return $this -> app['songshenzong.support'] -> getData($id, $last);
	}
}
