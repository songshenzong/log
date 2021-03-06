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

use Psr\Log\AbstractLogger;
use Songshenzong\Log\DataFormatter\DataFormatterInterface;

/**
 * Provides a way to log messages
 */
class MessagesCollector extends AbstractLogger implements DataCollectorInterface, MessagesAggregateInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var array
     */
    protected $aggregates = [];

    /**
     * @var
     */
    protected $dataFormater;

    /**
     * @param string $name
     */
    public function __construct($name = 'messages')
    {
        $this->name = $name;
    }

    /**
     * Sets the data formater instance used by this collector
     *
     * @param DataFormatterInterface $formater
     *
     * @return $this
     */
    public function setDataFormatter(DataFormatterInterface $formater)
    {
        $this->dataFormater = $formater;
        return $this;
    }

    /**
     * @return DataFormatterInterface
     */
    public function getDataFormatter()
    {
        if ($this->dataFormater === null) {
            $this->dataFormater = DataCollector::getDefaultDataFormatter();
        }
        return $this->dataFormater;
    }

    /**
     * Adds a message
     *
     * A message can be anything from an object to a string
     *
     * @param mixed  $message
     * @param string $label
     * @param bool   $isString
     */
    public function addMessage($message, $label = 'info', $isString = true)
    {
        if (!is_string($message)) {
            // $message = $this->getDataFormatter()->formatVar($message);
            $isString = false;
        }
        $this->messages[] = [
            'label'     => $label,
            'message'   => $message,
            'is_string' => $isString,
            'time'      => microtime(true),
        ];
    }

    /**
     * Aggregates messages from other collectors
     *
     * @param MessagesAggregateInterface $messages
     */
    public function aggregate(MessagesAggregateInterface $messages)
    {
        $this->aggregates[] = $messages;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        $messages = $this->messages;
        foreach ($this->aggregates as $collector) {
            $msgs     = array_map(function ($m) use ($collector) {
                $m['collector'] = $collector->getName();
                return $m;
            }, $collector->getMessages());
            $messages = array_merge($messages, $msgs);
        }

        // sort messages by their timestamp
        usort($messages, function ($a, $b) {
            if ($a['time'] === $b['time']) {
                return 0;
            }
            return $a['time'] < $b['time'] ? -1 : 1;
        });

        return $messages;
    }

    /**
     * @param       $level
     * @param       $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        $this->addMessage($message, $level);
    }

    /**
     * Deletes all messages
     */
    public function clear()
    {
        $this->messages = [];
    }

    /**
     * @return array
     */
    public function collect()
    {
        $messages = $this->getMessages();
        return [
            'count'    => count($messages),
            'messages' => $messages,
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
