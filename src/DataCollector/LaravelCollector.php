<?php

namespace Songshenzong\Log\DataCollector;

use Songshenzong\Log\DataCollector\DataCollector;
use Illuminate\Foundation\Application;

/**
 * {@inheritDoc}
 */

/**
 * Class LaravelCollector
 *
 * @package Songshenzong\Log\DataCollector
 */
class LaravelCollector extends DataCollector
{
    /** @var \Illuminate\Foundation\Application $app */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app = null)
    {
        $this->app = $app;
    }

    /**
     * Called by the DebugBar when data needs to be collected
     *
     * @return array Collected data
     */
    public function collect()
    {
        // Fallback if not injected
        $app = $this->app ?: app();

        return [
            'version'     => $app::VERSION,
            'environment' => $app->environment(),
            'locale'      => $app->getLocale(),
        ];
    }

    /**
     * Returns the unique name of the collector
     *
     * @return string
     */
    public function getName()
    {
        return 'laravel';
    }
}
