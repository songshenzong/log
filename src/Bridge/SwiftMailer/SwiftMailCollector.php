<?php
/*
 * This file is part of the package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\Log\Bridge\SwiftMailer;

use Songshenzong\Log\DataCollector\DataCollector;
use Swift_Mailer;
use Swift_Plugins_MessageLogger;

/**
 * Collects data about sent mails
 *
 * http://swiftmailer.org/
 */
class SwiftMailCollector extends DataCollector
{
    /**
     * @var Swift_Plugins_MessageLogger
     */
    protected $messagesLogger;

    /**
     * SwiftMailCollector constructor.
     *
     * @param Swift_Mailer $mailer
     */
    public function __construct(Swift_Mailer $mailer)
    {
        $this->messagesLogger = new Swift_Plugins_MessageLogger();
        $mailer->registerPlugin($this->messagesLogger);
    }

    /**
     * {@inheritDoc}
     */
    /**
     * @return array
     */
    public function collect()
    {
        $mails = [];
        foreach ($this->messagesLogger->getMessages() as $msg) {
            $mails[] = [
                'to'      => $this->formatTo($msg->getTo()),
                'subject' => $msg->getSubject(),
                'headers' => $msg->getHeaders()->toString()
            ];
        }
        return [
            'count' => count($mails),
            'mails' => $mails
        ];
    }

    /**
     * @param $to
     *
     * @return string
     */
    protected function formatTo($to)
    {
        if (!$to) {
            return '';
        }

        $f = [];
        foreach ($to as $k => $v) {
            $f[] = (empty($v) ? '' : "$v ") . "<$k>";
        }
        return implode(', ', $f);
    }

    /**
     * {@inheritDoc}
     */
    /**
     * @return string
     */
    public function getName()
    {
        return 'swiftmailer_mails';
    }
}
