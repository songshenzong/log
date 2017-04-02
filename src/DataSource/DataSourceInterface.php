<?php

namespace Songshenzong\Log\DataSource;

use Songshenzong\Log\Request\Request;

/**
 * Data source interface, all data sources must implement this interface
 */
interface DataSourceInterface {
	
	/**
	 * Adds data to the request and returns it
	 */
	public function resolve(Request $request);
}
