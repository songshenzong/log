<?php

namespace Songshenzong\Storage;

use Songshenzong\Songshenzong;
use Songshenzong\Request\Request;

/**
 * SQL storage for requests
 */
class SqlStorage extends Storage {
	
	/**
	 * Name of the table with Songshenzong requests metadata
	 */
	protected $table;
	
	/**
	 * List of Request keys that need to be serialized before they can be stored in database
	 */
	protected $needs_serialization = [
		'headers',
		'getData',
		'postData',
		'sessionData',
		'cookies',
		'databaseQueries',
		'timelineData',
		'log',
		'routes',
		'emailsData',
		'viewsData',
		'userData'
	];
	
	/**
	 * Return a new storage, takes PDO object or DSN and optionally a table name and database credentials as arguments
	 */
	public function __construct($table) {
		
		$this -> table = $table;
	}
	
	/**
	 * Retrieve a request specified by id argument, if second argument is specified, array of requests from id to last
	 * will be returned
	 */
	public function retrieve($id = NULL, $last = NULL) {
		if ( ! $id) {
			$stmt = $this -> pdo -> prepare('SELECT (id, version, time, method, uri, headers, controller, getData, postData, sessionData, cookies, responseTime, responseStatus, responseDuration, databaseQueries, databaseDuration, timelineData, log, routes, emailsData, viewsData, userData) ' . "FROM {$this->table} ");
			
			$stmt -> execute();
			$data = $stms -> fetchAll(PDO::FETCH_ASSOC);
			
			$requests = [];
			
			foreach ($data as $item) {
				$requests[] = $this -> createRequestFromData($item);
			}
			
			return $requests;
		}
		
		$stmt = $this -> pdo -> prepare('SELECT id, version, time, method, uri, headers, controller, getData, postData, sessionData, cookies, responseTime, responseStatus, responseDuration, databaseQueries, databaseDuration, timelineData, log, routes, emailsData, viewsData, userData ' . "FROM {$this->table} " . 'WHERE id = :id');
		
		$stmt -> execute(['id' => $id]);
		$data = $stmt -> fetch(PDO::FETCH_ASSOC);
		
		if ( ! $data) {
			return NULL;
		}
		
		if ( ! $last) {
			return $this -> createRequestFromData($data);
		}
		
		$stmt = $this -> pdo -> prepare('SELECT (id, version, time, method, uri, headers, controller, getData, postData, sessionData, cookies, responseTime, responseStatus, responseDuration, databaseQueries, databaseDuration, timelineData, log, routes, emailsData, viewsData, userData) ' . "FROM {$this->table} " . "WHERE id = :id");
		
		$stmt -> execute(['id' => $last]);
		$last_data = $stmt -> fetch(PDO::FETCH_ASSOC);
		
		$stmt = $this -> pdo -> prepare('SELECT (id, version, time, method, uri, headers, controller, getData, postData, sessionData, cookies, responseTime, responseStatus, responseDuration, databaseQueries, databaseDuration, timelineData, log, routes, emailsData, viewsData, userData) ' . "FROM {$this->table} " . "WHERE time >= :from AND time <= :to");
		
		$stmt -> execute([
			                 'from' => $data['time'],
			                 'to'   => $last_data['time']
		                 ]);
		$data = $stmt -> fetchAll(PDO::FETCH_ASSOC);
		
		$requests = [];
		
		foreach ($data as $item) {
			$requests[] = $this -> createRequestFromData($item);
		}
		
		return $requests;
	}
	
	/**
	 * Store the request in the database
	 */
	public function store(Request $request) {
		$data = $this -> applyFilter($request -> toArray());
		
		foreach ($this -> needs_serialization as $key) {
			$data[$key] = @json_encode($data[$key]);
		}
		
		$data['version'] = Songshenzong::VERSION;
		Sql ::create($data);
		
		header("Content-Type:application/json");
		echo json_encode($request -> toArray());
		exit;
	}
	
	/**
	 * Create the metadata table if it doesn't exist
	 */
	public function initialize() {
		
		if (Sql ::  get()) {
			return;
		}
		
		\DB ::statement("CREATE TABLE {$this->table} (" . 'id VARCHAR(100), ' . 'version INTEGER, ' . 'time DOUBLE NULL, ' . 'method VARCHAR(10) NULL, ' . 'uri VARCHAR(250) NULL, ' . 'headers MEDIUMTEXT NULL, ' . 'controller VARCHAR(250) NULL, ' . 'getData MEDIUMTEXT NULL, ' . 'postData MEDIUMTEXT NULL, ' . 'sessionData MEDIUMTEXT NULL, ' . 'cookies MEDIUMTEXT NULL, ' . 'responseTime DOUBLE NULL, ' . 'responseStatus INTEGER NULL, ' . 'responseDuration DOUBLE NULL, ' . 'databaseQueries MEDIUMTEXT NULL, ' . 'databaseDuration DOUBLE NULL, ' . 'timelineData MEDIUMTEXT NULL, ' . 'log MEDIUMTEXT NULL, ' . 'routes MEDIUMTEXT NULL, ' . 'emailsData MEDIUMTEXT NULL, ' . 'viewsData MEDIUMTEXT NULL, ' . 'userData MEDIUMTEXT NULL' . ');');
		
		
	}
	
	protected function createRequestFromData($data) {
		foreach ($this -> needs_serialization as $key) {
			$data[$key] = json_decode($data[$key], TRUE);
		}
		
		return new Request($data);
	}
}
