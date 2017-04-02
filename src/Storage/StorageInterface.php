<?php

namespace Songshenzong\Log\Storage;

use Songshenzong\Log\Request\Request;

/**
 * Base storage class, all storage have to extend this class
 */
interface StorageInterface {
	
	/**
	 * Retrieve request specified by id argument, if second argument is specified, array of requests from id to last
	 * will be returned
	 */
	public function retrieve($id = NULL, $last = NULL);
	
	/**
	 * Store request
	 */
	public function store(Request $request);
}
