<?php

namespace Songshenzong\Support\Laravel;

use Songshenzong\Songshenzong;
use Songshenzong\Storage\SqlStorage;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;

class Support {
	
	/**
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app;
	
	/**
	 * Support constructor.
	 *
	 * @param \Illuminate\Foundation\Application $app
	 */
	public function __construct(Application $app) {
		
		$this -> app = $app;
		
	}
	
	/**
	 * @return mixed
	 */
	public function getAdditionalDataSources() {
		return $this -> getConfig('additional_data_sources', []);
	}
	
	/**
	 * @param      $key
	 * @param null $default
	 *
	 * @return mixed
	 */
	public function getConfig($key, $default = NULL) {
		
		if ($this -> app['config'] -> has("songshenzong::songshenzong.{$key}")) {
			// try to look for a value from songshenzong.php configuration file first
			return $this -> app['config'] -> get("songshenzong::songshenzong.{$key}");
		} else {
			// try to look for a value from config.php (pre 1.7) or return the default value
			return $this -> app['config'] -> get("songshenzong::config.{$key}", $default);
		}
		
	}
	
	/**
	 * @param null $id
	 * @param null $last
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getData($id = NULL, $last = NULL) {
		$this -> app['session.store'] -> reflash();
		
		return new JsonResponse($this -> app['songshenzong'] -> getStorage()
		                                                     -> retrieve($id, $last));
	}
	
	/**
	 * @return \Songshenzong\Storage\SqlStorage
	 */
	public function getStorage() {
		
		$table = $this -> getConfig('storage_table', 'songshenzong');
		
		$storage = new SqlStorage($table);
		$storage -> initialize();
		
		$storage -> filter = $this -> getFilter();
		
		return $storage;
	}
	
	/**
	 * @return mixed
	 */
	public function getFilter() {
		return $this -> getConfig('filter', []);
	}
	
	/**
	 * @param $request
	 * @param $response
	 *
	 * @return mixed
	 */
	public function process($request, $response) {
		if ( ! $this -> isCollectingData()) {
			return $response; // Collecting data is disabled, return immediately
		}
		
		// don't collect data for configured URIs
		$request_uri   = $request -> getRequestUri();
		$filter_uris   = $this -> getConfig('filter_uris', []);
		$filter_uris[] = '/__songshenzong/[0-9\.]+'; // don't collect data for Songshenzong requests
		
		foreach ($filter_uris as $uri) {
			$regexp = '#' . str_replace('#', '\#', $uri) . '#';
			
			if (preg_match($regexp, $request_uri)) {
				return $response;
			}
		}
		
		$this -> app['songshenzong.laravel'] -> setResponse($response);
		
		$this -> app['songshenzong'] -> resolveRequest();
		$this -> app['songshenzong'] -> storeRequest();
		
		if ( ! $this -> isEnabled()) {
			return $response; // Songshenzong is disabled, don't set the headers
		}
		
		$response -> headers -> set('X-Clockwork-Id', $this -> app['songshenzong'] -> getRequest() -> id, TRUE);
		$response -> headers -> set('X-Clockwork-Version', Songshenzong::VERSION, TRUE);
		
		if ($request -> getBasePath()) {
			$response -> headers -> set('X-Clockwork-Path', $request -> getBasePath() . '/__songshenzong/', TRUE);
		}
		
		$extra_headers = $this -> getConfig('headers', []);
		foreach ($extra_headers as $header_name => $header_value) {
			$response -> headers -> set('X-Clockwork-Header-' . $header_name, $header_value);
		}
		
		return $response;
	}
	
	/**
	 * @return mixed
	 */
	public function isEnabled() {
		$is_enabled = $this -> getConfig('enable', NULL);
		
		if ($is_enabled === NULL) {
			$is_enabled = env('APP_DEBUG');
		}
		
		return $is_enabled;
	}
	
	/**
	 * @return bool
	 */
	public function isCollectingData() {
		return $this -> isEnabled() || $this -> getConfig('collect_data_always', FALSE);
	}
	
	/**
	 * @return bool
	 */
	public function isCollectingDatabaseQueries() {
		return $this -> app['config'] -> get('database.default') && ! in_array('databaseQueries', $this -> getFilter());
	}
}
