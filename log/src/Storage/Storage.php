<?php

namespace Songshenzong\Storage;

use Songshenzong\Request\Request;

/**
 * Base storage class, all storage have to extend this class
 */
abstract class Storage implements StorageInterface {
	
	/**
	 * Array of data to be filtered from stored requests
	 */
	public $filter = [];
	
	/**
	 * Same as retrieve, but json representations of requests are returned
	 */
	public function retrieveAsJson($id = NULL, $last = NULL) {
		$requests = $this -> retrieve($id, $last);
		
		if ( ! $requests) {
			return NULL;
		}
		
		if ( ! is_array($requests)) {
			return $requests -> toJson();
		}
		
		foreach ($requests as &$request) {
			$request = $request -> toArray();
		}
		
		return json_encode($requests);
	}
	
	/**
	 * Return array of data with applied filter
	 */
	protected function applyFilter(array $data) {
		$emptyRequest = new Request([]);
		
		foreach ($this -> filter as $key) {
			if (isset($data[$key])) {
				$data[$key] = $emptyRequest -> $key;
			}
		}
		
		return $data;
	}
}
