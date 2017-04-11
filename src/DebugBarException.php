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

use Throwable;

class DebugBarException extends \Exception
{
    public function __construct($message)
    {
        dd($message);
    }
}
