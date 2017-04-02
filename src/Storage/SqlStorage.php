<?php

namespace Songshenzong\Log\Storage;

use Songshenzong\Log\Songshenzong;
use Songshenzong\Log\Request\Request;

/**
 * SQL storage for requests
 */
class SqlStorage extends Storage
{

    /**
     * Name of the table with Songshenzong requests metadata
     */
    protected $table;

    /**
     * SqlStorage constructor.
     */
    public function __construct()
    {
        $this -> table = 'songshenzong_logs';
        $statement     = <<<HEREDOC
		CREATE TABLE IF NOT EXISTS {$this -> table}
		(
			id               INT UNSIGNED                        NOT NULL AUTO_INCREMENT PRIMARY KEY,
			version           VARCHAR(20)                         NULL,
			time              VARCHAR(20)                         NULL,
			method            VARCHAR(10)                         NULL,
			uri               VARCHAR(250)                        NULL,
			headers           MEDIUMTEXT                          NULL,
			controller        VARCHAR(250)                        NULL,
			get_data          MEDIUMTEXT                          NULL,
			post_data         MEDIUMTEXT                          NULL,
			session_data      MEDIUMTEXT                          NULL,
			cookies           MEDIUMTEXT                          NULL,
			response_time     DOUBLE                              NULL,
			response_status   INT                                 NULL,
			response_duration DOUBLE                              NULL,
			database_queries  MEDIUMTEXT                          NULL,
			database_duration DOUBLE                              NULL,
			time_line_data    MEDIUMTEXT                          NULL,
			log               MEDIUMTEXT                          NULL,
			routes            MEDIUMTEXT                          NULL,
			emails_data       MEDIUMTEXT                          NULL,
			views_data        MEDIUMTEXT                          NULL,
			user_data         MEDIUMTEXT                          NULL,
			created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
			updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
		);
HEREDOC;

        $this -> createTableAndLock($statement, $this -> table);


    }

    /**
     * @param $statement
     * @param $table
     *
     * @return bool
     */
    private function createTableAndLock($statement, $table)
    {

        $lock = __DIR__ . '/' . studly_case($table) . '.lock';

        if (!file_exists($lock)) {

            IF (\DB ::statement("DROP TABLE IF EXISTS {$table};")) {
                if (\DB ::statement($statement)) {
                    file_put_contents($lock, 'ok');
                    return true;
                }
            }

        }

    }

    /**
     * Retrieve a request specified by id argument, if second argument is specified, array of requests from id to last
     * will be returned
     *
     * @param null $id
     * @param null $last
     *
     * @return mixed
     */
    public function retrieve($id = null, $last = null)
    {
        return SongshenzongLog ::find($id);
    }

    /**
     * Store the request in the database
     */
    public function store(Request $request)
    {

        $data            = $this -> applyFilter($request -> toArray());
        $data['version'] = Songshenzong::VERSION;

        return SongshenzongLog ::create($data);
    }

}
