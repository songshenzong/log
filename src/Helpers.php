<?php


if (!function_exists('songshenzongLog')) {
    /**
     * Get the instance
     *
     * @return \Songshenzong\Log\LaravelDebugbar
     */
    function songshenzongLog()
    {
        return app('songshenzongLog');
    }
}


if (!function_exists('log')) {
    /**
     * Get the instance
     *
     * @return \Songshenzong\Log\LaravelDebugbar
     */
    function log()
    {
        return app('songshenzongLog');
    }
}


if (!function_exists('debug')) {
    /**
     * Adds one or more messages to the MessagesCollector
     *
     * @param  mixed ...$value
     *
     * @return string
     */
    function debug($value)
    {
        foreach (func_get_args() as $value) {
            app('songshenzongLog')->addMessage($value, 'debug');
        }
    }
}


if (!function_exists('addMessage')) {
    /**
     * Adds one or more messages to the MessagesCollector
     *
     * @param  mixed ...$value
     *
     * @param string $label
     *
     * @return string
     */
    function addMessage($value, $label = 'info')
    {
        app('songshenzongLog')->addMessage($value, $label);
    }
}


if (!function_exists('start_measure')) {
    /**
     * Starts a measure
     *
     * @param string $name  Internal name, used to stop the measure
     * @param string $label Public name
     */
    function start_measure($name, $label = null)
    {
        app('songshenzongLog')->startMeasure($name, $label);
    }
}


if (!function_exists('stop_measure')) {
    /**
     * Stop a measure
     *
     * @param string $name Internal name, used to stop the measure
     */
    function stop_measure($name)
    {
        app('songshenzongLog')->stopMeasure($name);
    }
}


if (!function_exists('add_measure')) {
    /**
     * Adds a measure
     *
     * @param string $label
     * @param float  $start
     * @param float  $end
     */
    function add_measure($label, $start, $end)
    {
        app('songshenzongLog')->addMeasure($label, $start, $end);
    }
}


if (!function_exists('measure')) {
    /**
     * Utility function to measure the execution of a Closure
     *
     * @param string   $label
     * @param \Closure $closure
     */
    function measure($label, \Closure $closure)
    {
        app('songshenzongLog')->measure($label, $closure);
    }
}
