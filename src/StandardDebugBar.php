<?php
/*
 * This file is part of the package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\RequestLog;

use Songshenzong\RequestLog\DataCollector\ExceptionsCollector;
use Songshenzong\RequestLog\DataCollector\MemoryCollector;
use Songshenzong\RequestLog\DataCollector\MessagesCollector;
use Songshenzong\RequestLog\DataCollector\PhpInfoCollector;
use Songshenzong\RequestLog\DataCollector\RequestCollector;
use Songshenzong\RequestLog\DataCollector\TimeDataCollector;

/**
 * Debug bar subclass which adds all included collectors
 */
class StandardDebugBar extends DebugBar
{
    public function __construct()
    {
        $this->addCollector(new PhpInfoCollector());
        $this->addCollector(new MessagesCollector());
        $this->addCollector(new RequestCollector());
        $this->addCollector(new TimeDataCollector());
        $this->addCollector(new MemoryCollector());
        $this->addCollector(new ExceptionsCollector());
    }
}
