<?php

namespace Songshenzong\Storage;

use Illuminate\Database\Eloquent\Model;

class Sql extends Model {
	
	protected $table    = 'songshenzong';
	protected $fillable = [
		'version',
		'time',
		'method',
		'uri',
		'headers',
		'controller',
		'getData',
		'postData',
		'sessionData',
		'cookies',
		'responseTime',
		'responseStatus',
		'responseDuration',
		'databaseQueries',
		'databaseDuration',
		'timelineData',
		'log',
		'routes',
		'emailsData',
		'viewsData',
		'userData',
	];
}
