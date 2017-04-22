<?php

namespace Songshenzong\RequestLog\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Songshenzong\RequestLog\RequestLog;
use Songshenzong\RequestLog\LaravelDebugbar;

class ApiController extends BaseController
{

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    public $app;


    /**
     * The Songshenzong instance
     *
     * @var LaravelDebugbar
     */
    protected $songshenzong;


    /**
     * @var string\
     */
    protected $table;


    /**
     * CurrentController constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app, LaravelDebugbar $songshenzong)
    {
        $this -> app          = $app;
        $this -> table        = config('request-log.table', 'request_logs');
        $this -> songshenzong = $songshenzong;
    }


    /**
     * Create table.
     */
    public function createTable()
    {
        $this -> songshenzong -> stopCollect();
        \DB ::statement("DROP TABLE IF EXISTS {$this->table};");

        $createTable = <<<"HEREDOC"
CREATE TABLE IF NOT EXISTS {$this -> table}
(
  id         INT UNSIGNED     NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
  time       VARCHAR(100)     NOT NULL,
  ip         VARCHAR(50)      NOT NULL,
  method     VARCHAR(10)      NOT NULL,
  uri        VARCHAR(300)     NOT NULL,
  data       LONGTEXT         NOT NULL,
  created_at TIMESTAMP        NULL,
  updated_at TIMESTAMP        NULL
);
HEREDOC;


        if (\DB ::statement($createTable)) {
            \DB ::statement("CREATE INDEX {$this->table}_created_at_index ON {$this->table} (created_at);");
            \DB ::statement("CREATE INDEX {$this->table}_ip_index ON {$this->table} (ip);");
            \DB ::statement("CREATE INDEX {$this->table}_method_index ON {$this->table} (method);");
            \DB ::statement("CREATE INDEX {$this->table}_uri_index ON {$this->table} (uri);");
            \DB ::statement("CREATE INDEX {$this->table}_time_index ON {$this->table} (time);");
            $this -> songshenzong -> startCollect();
            return $this -> getList();
        }
        return $this -> songshenzong -> json(500, 'Error');
    }


    /**
     * @param                                     $id
     * @param \Songshenzong\RequestLog\RequestLog $songshenzong_log
     *
     * @return mixed
     */
    public function getItem($id, RequestLog $songshenzong_log)
    {
        return $this -> songshenzong -> item($songshenzong_log -> find($id));
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        $index = isset(\request() -> per_page) ? \request() -> per_page : 30;

        $list = RequestLog :: orderBy('created_at', 'desc')
                           -> paginate($index)
                           -> appends(\request() -> all())
                           -> toArray();


        return $this -> songshenzong -> json(200, 'OK', $list);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        if (\request() -> has('id')) {
            return RequestLog ::destroy(\request() -> id);
        }

        RequestLog ::where('id', '!=', 0) -> delete();

        return $this -> getList();
    }

    /**
     * Get Collect Status.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrSetCollectStatus()
    {
        if (request() -> has('set') && request() -> set == 'true') {
            if ($this -> songshenzong -> isCollect()) {
                $this -> songshenzong -> stopCollect();
            } else {
                // Check Logs Table Status
                if (!$this -> isTableExists()) {
                    return $this -> songshenzong -> json(400, 'Please Create Logs Table First.');
                }
                $this -> songshenzong -> startCollect();
            }
        }


        if ($status = $this -> songshenzong -> isCollect()) {
            $message = 'Collect Enabled';
        } else {
            $message = 'Collect Disabled';
        }

        return $this -> songshenzong -> json(200, $message, ['enable' => $status]);
    }


    /**
     * Get Table Status.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTableStatus()
    {
        if ($status = $this -> isTableExists()) {
            $message = 'Recreate Logs Table';
        } else {
            $message = 'Create Logs Table';
        }


        return $this -> songshenzong -> json(200, $message, ['enable' => $status]);
    }

    /**
     * Check the Logs table exists.
     *
     * @return bool
     */
    public function isTableExists()
    {
        $status = (bool)\DB ::select("SHOW TABLES LIKE '" . $this -> table . "';");
        if (!$status) {
            $this -> songshenzong -> stopCollect();
        }
        return $status;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function login(Request $request)
    {
        if ($request -> has('token')) {
            $tokens = config('request-log.token', ['songshenzong']);
            if (in_array($request -> token, $tokens)) {
                return $this -> songshenzong -> json(200, 'OK');
            }

            return $this -> songshenzong -> json(403, $request -> token . ' is Invalid Token !');
        }

        return $this -> songshenzong -> json(403, 'No Token !');
    }
}
