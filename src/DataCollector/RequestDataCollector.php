<?php
/*
 * This file is part of the package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\Log\DataCollector;

/**
 * Collects info about the current request
 */
class RequestDataCollector extends DataCollector
{
    /**
     * @return array
     */
    public function collect()
    {
        $vars = array('GET', 'POST', 'SESSION', 'COOKIE', 'SERVER');
        $data = array();

        foreach ($vars as $var) {
            if (isset($GLOBALS['_' . $var])) {
                $data[$var] = $GLOBALS['_' . $var];

            }
        }
        $data = array_change_key_case($data, CASE_LOWER);
        return $data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'request';
    }


}
