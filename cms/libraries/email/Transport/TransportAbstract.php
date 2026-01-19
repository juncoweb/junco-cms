<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Email\Transport;

use Email;

abstract class TransportAbstract implements TransportInterface
{
    // debug
    protected $debug        = false;
    protected $debug_log    = [];

    /**
     * Debug
     * 
     * @param bool $debug
     */
    public function debug(bool $debug = true)
    {
        $this->debug = $debug;
    }

    /**
     * Debug Output
     * 
     * @param int $mode
     * 
     * @return string|array
     */
    public function debugOutput(int $mode = 1): string|array
    {
        $debug_log            = $this->debug_log;
        $this->debug_log    = [];

        switch ($mode) {
            case 1:
                return implode(PHP_EOL, $debug_log) . PHP_EOL;

            case 2:
                foreach ($debug_log as $i => $line) {
                    $debug_log[$i] = '<pre>' . htmlentities($line) . '</pre>';
                }
                return implode($debug_log);
            case 3:
                return implode('<br />', $debug_log) . '<br />';
        }

        return $debug_log;
    }
}
