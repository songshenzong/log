<?php

namespace Songshenzong\Log\Storage;

use Illuminate\Database\Eloquent\Model;

class SongshenzongLog extends Model {
	
	protected $fillable = [
		'version',
		'time',
		'method',
		'uri',
		'headers',
		'controller',
		'get_data',
		'post_data',
		'session_data',
		'cookies',
		'response_time',
		'response_status',
		'response_duration',
		'database_queries',
		'database_duration',
		'time_line_data',
		'log',
		'routes',
		'emails_data',
		'views_data',
		'user_data',
	];
	
	/**
	 *
	 * @var array
	 */
	protected $casts = [
		'headers'          => 'array',
		'get_data'         => 'array',
		'post_data'        => 'array',
		'session_data'     => 'array',
		'cookies'          => 'array',
		'database_queries' => 'array',
		'time_line_data'   => 'array',
		'log'              => 'array',
		'routes'           => 'array',
		'emails_data'      => 'array',
		'views_data'       => 'array',
		'user_data'        => 'array',
	
	];
	
	// public function getLogAttribute($key) {
	// 	$key = json_decode($key, TRUE);
	//
	// 	$key = array_map(function ($i) {
	// 		$i['message'] = json_decode($i['message']);
	//
	// 		return $i;
	// 	}, $key);
	//
	// 	return $key;
	// }
	
	public function getTimeAttribute($key) {
		list($usec, $sec) = explode(".", $key);
		$date = date('d/m/y H:i:s x-ms', $usec);
		
		return str_replace('x', $sec, $date);
		
	}
	
}
