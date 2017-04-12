<?php namespace Songshenzong\RequestLog\Console;

use Songshenzong\RequestLog\DebugBar;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
    protected $name = 'songshenzong:clear';
    protected $description = 'Clear the Songshenzong Storage';
    protected $debugbar;

    public function __construct(DebugBar $debugbar)
    {
        $this->debugbar = $debugbar;

        parent::__construct();
    }

    public function fire()
    {
        $this->debugbar->boot();


    }
}
