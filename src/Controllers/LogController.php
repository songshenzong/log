<?php

namespace Songshenzong\Log\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Controller;
use Songshenzong\Log\SongshenzongLog;

class LogController extends Controller
{

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    public $app;

    /**
     * CurrentController constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this -> app = $app;
    }

    /**
     * @param null $id
     * @param null $last
     *
     * @return mixed
     */
    public function getData($id)
    {
        $log = SongshenzongLog ::find($id);

        return $log;
    }

    public function getList()
    {
        return SongshenzongLog :: orderBy('created_at', 'desc')
                               -> paginate(\request() -> per_page??23)
                               -> appends(\request() -> all())
                               -> toArray();
    }

    public function index()
    {
        return file_get_contents(__DIR__ . '/../Views/index.html');
    }

    public function destroy()
    {
        if (\request() -> has('id')) {
            return SongshenzongLog ::destroy(\request() -> id);
        }
        SongshenzongLog ::where('id', '!=', 0) -> delete();

        return $this -> getList();

    }
}
