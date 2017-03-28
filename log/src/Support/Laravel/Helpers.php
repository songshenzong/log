<?php

if ( ! function_exists('songshenzong')) {
	// workaround so we can log null values with the helper function
	if ( ! defined('SONGSHENZONG_NULL')) {
		define('SONGSHENZONG_NULL', sha1(time()));
	}
	
	/**
	 * Log a message to Songshenzong, returns Songshenzong instance when called with no arguments.
	 */
	function clock($message = SONGSHENZONG_NULL) {
		if ($message === SONGSHENZONG_NULL) {
			return app('songshenzong');
		} else {
			foreach (func_get_args() as $arg) {
				app('songshenzong') -> debug($arg);
			}
		}
	}
}
