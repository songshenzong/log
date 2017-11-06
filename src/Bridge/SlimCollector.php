<?php
/*
 * This file is part of the package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\Log\Bridge;

use Songshenzong\Log\DataCollector\MessagesCollector;
use Psr\Log\LogLevel;
use Slim\Log;
use Slim\Slim;

/**
 * Collects messages from a Slim logger
 *
 * http://slimframework.com
 */
class SlimCollector extends MessagesCollector
{
    /**
     * @var Slim
     */
    protected $slim;

    /**
     * @var
     */
    protected $originalLogWriter;

    /**
     * SlimCollector constructor.
     *
     * @param Slim $slim
     */
    public function __construct(Slim $slim)
    {
        $this->slim = $slim;
        if ($log = $slim->getLog()) {
            $this->originalLogWriter = $log->getWriter();
            $log->setWriter($this);
            $log->setEnabled(true);
        }
    }

    /**
     * @param $message
     * @param $level
     */
    public function write($message, $level)
    {
        if ($this->originalLogWriter) {
            $this->originalLogWriter->write($message, $level);
        }
        $this->addMessage($message, $this->getLevelName($level));
    }

    /**
     * @param $level
     *
     * @return mixed
     */
    protected function getLevelName($level)
    {
        $map = [
            Log::EMERGENCY => LogLevel::EMERGENCY,
            Log::ALERT     => LogLevel::ALERT,
            Log::CRITICAL  => LogLevel::CRITICAL,
            Log::ERROR     => LogLevel::ERROR,
            Log::WARN      => LogLevel::WARNING,
            Log::NOTICE    => LogLevel::NOTICE,
            Log::INFO      => LogLevel::INFO,
            Log::DEBUG     => LogLevel::DEBUG,
        ];
        return $map[$level];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'slim';
    }
}
