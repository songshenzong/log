<?php

namespace Songshenzong\Log\DataCollector;

use Songshenzong\Log\DataCollector\MessagesCollector;
use Psr\Log\LogLevel;
use ReflectionClass;

/**
 * Class LogsCollector
 *
 * @package Songshenzong\Log\DataCollector
 */
class LogsCollector extends MessagesCollector
{
    /**
     * @var int
     */
    protected $lines = 124;

    /**
     * LogsCollector constructor.
     *
     * @param null   $path
     * @param string $name
     */
    public function __construct($path = null, $name = 'logs')
    {
        parent::__construct($name);

        $path = $path ?: $this->getLogsFile();
    }

    /**
     * Get the path to the logs file
     *
     * @return string
     */
    public function getLogsFile()
    {
        // default daily rotating logs (Laravel 5.0)
        $path = storage_path() . '/logs/laravel-' . date('Y-m-d') . '.log';

        // single file logs
        if (!file_exists($path)) {
            $path = storage_path() . '/logs/laravel.log';
        }

        return $path;
    }


    /**
     * By Ain Tohvri (ain)
     * http://tekkie.flashbit.net/php/tail-functionality-in-php
     *
     * @param string $file
     * @param int    $lines
     *
     * @return array
     */
    protected function tailFile($file, $lines)
    {
        $handle      = fopen($file, 'rb');
        $linecounter = $lines;
        $pos         = -2;
        $beginning   = false;
        $text        = [];
        while ($linecounter > 0) {
            $t = ' ';
            while ($t != "\n") {
                if (fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos--;
            }
            $linecounter--;
            if ($beginning) {
                rewind($handle);
            }
            $text[$lines - $linecounter - 1] = fgets($handle);
            if ($beginning) {
                break;
            }
        }
        fclose($handle);
        return array_reverse($text);
    }

    /**
     * Search a string for log entries
     * Based on https://github.com/mikemand/logviewer/blob/master/src/Kmd/Logviewer/Logviewer.php by mikemand
     *
     * @param $file
     *
     * @return array
     */
    public function getLogs($file)
    {
        $pattern = "/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/";

        $log_levels = $this->getLevels();

        // There has GOT to be a better way of doing this...
        preg_match_all($pattern, $file, $headings);
        $log_data = preg_split($pattern, $file);

        $log = [];
        foreach ($headings as $h) {
            for ($i = 0, $j = count($h); $i < $j; $i++) {
                foreach ($log_levels as $ll) {
                    if (strpos(strtolower($h[$i]), strtolower('.' . $ll))) {
                        $log[] = ['level' => $ll, 'header' => $h[$i], 'stack' => $log_data[$i]];
                    }
                }
            }
        }

        $log = array_reverse($log);

        return $log;
    }

    /**
     * Get the log levels from psr/log.
     * Based on https://github.com/mikemand/logviewer/blob/master/src/Kmd/Logviewer/Logviewer.php by mikemand
     *
     * @access public
     * @return array
     */
    public function getLevels()
    {
        $class = new ReflectionClass(new LogLevel());
        return $class->getConstants();
    }
}
