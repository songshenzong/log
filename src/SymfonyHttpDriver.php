<?php

namespace Songshenzong\Log;

use Songshenzong\Log\HttpDriverInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * HTTP driver for Symfony Request/Session
 */
class SymfonyHttpDriver implements HttpDriverInterface
{
    /** @var \Symfony\Component\HttpFoundation\Session\Session|\Illuminate\Contracts\Session\Session|\Illuminate\Session\SessionManager */
    protected $session;
    /** @var \Symfony\Component\HttpFoundation\Response */
    protected $response;

    /**
     * SymfonyHttpDriver constructor.
     *
     * @param      $session
     * @param null $response
     */
    public function __construct($session, $response = null)
    {
        $this->session  = $session;
        $this->response = $response;
    }

    /**
     * Sets HTTP headers
     *
     * @param array $headers
     *
     */
    public function setHeaders(array $headers)
    {
        if (null !== $this->response) {
            $this->response->headers->add($headers);
        }
    }

    /**
     * Checks if the session is started
     *
     * @return boolean
     */
    public function isSessionStarted()
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }
        return $this->session->isStarted();
    }

    /**
     * Sets a value in the session
     *
     * @param string $name
     * @param string $value
     */
    public function setSessionValue($name, $value)
    {
        // In Laravel 5.4 the session changed to use their own custom implementation
        // instead of the one from Symfony. One of the changes was the set method
        // that was changed to put. Here we check if we are using the new one.
        if (method_exists($this->session, 'driver') && $this->session->driver() instanceof \Illuminate\Contracts\Session\Session) {
            $this->session->put($name, $value);
            return;
        }
        $this->session->set($name, $value);
    }

    /**
     * Checks if a value is in the session
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasSessionValue($name)
    {
        return $this->session->has($name);
    }

    /**
     * Returns a value from the session
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getSessionValue($name)
    {
        return $this->session->get($name);
    }

    /**
     * Deletes a value from the session
     *
     * @param string $name
     */
    public function deleteSessionValue($name)
    {
        $this->session->remove($name);
    }
}
