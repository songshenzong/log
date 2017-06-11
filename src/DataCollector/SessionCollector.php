<?php

namespace Songshenzong\Log\DataCollector;

use Songshenzong\Log\DataCollector\DataCollector;
use Songshenzong\Log\DataCollector\DataCollectorInterface;

/**
 * {@inheritDoc}
 */

/**
 * Class SessionCollector
 *
 * @package Songshenzong\Log\DataCollector
 */
class SessionCollector extends DataCollector implements DataCollectorInterface
{
    /** @var  \Symfony\Component\HttpFoundation\Session\SessionInterface|\Illuminate\Contracts\Session\Session $session */
    protected $session;

    /**
     * Create a new SessionCollector
     *
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface|\Illuminate\Contracts\Session\Session $session
     */
    public function __construct($session)
    {
        $this -> session = $session;
    }

    /**
     * Called by the DebugBar when data needs to be collected
     *
     * @return array Collected data
     */
    public function collect()
    {
        return $this -> session -> all();
    }

    /**
     * Returns the unique name of the collector
     *
     * @return string
     */
    public function getName()
    {
        return 'session';
    }
}
