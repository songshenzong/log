<?php namespace Songshenzong\Log\Console;

use Songshenzong\Log\DebugBar;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
    protected $name = 'debugbar:clear';
    protected $description = 'Clear the Debugbar Storage';
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
