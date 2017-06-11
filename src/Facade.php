<?php

namespace Songshenzong\Log;

/**
 * Class Facade
 *
 * @package Songshenzong\Log
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'songshenzongLog';
    }
}
