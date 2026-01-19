<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Profiler
{
    // vars
    protected $itime   = null;
    protected $imemory = 0;
    protected $marks   = [];
    protected $router  = null;

    /**
     * Constructor
     */
    public function __construct(bool $from_outset = false)
    {
        if ($from_outset) {
            $this->itime = $_SERVER['REQUEST_TIME_FLOAT'];
        } else {
            $this->itime = microtime(true);
            $this->imemory = memory_get_usage();
        }
    }

    /**
     * Creates an entry in the profiler
     * 
     * @param string $label
     * @param mixed ...$args   Values to include in the label.
     */
    public function mark(string $label = '', ...$args)
    {
        if ($args) {
            array_walk($args, function (&$value) {
                if (is_array($value)) {
                    $value = var_export($value, true);
                } elseif (is_null($value)) {
                    $value = 'NULL';
                } elseif (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                } elseif (is_string($value)) {
                    $value = "'$value'";
                }
            });
            array_unshift($args, $label);
            $label = call_user_func_array('sprintf', $args);
        }

        $this->marks[] = [
            'label'        => $label,
            'time'        => microtime(true),
            'memory'    => memory_get_usage()
        ];
    }

    /**
     * Console
     * 
     * @param bool $only_marks
     */
    public function render(bool $full = false)
    {
        $this->mark('When rendering the console');

        $html = $this->renderMarksData();

        if ($full) {
            $html .= $this->renderRouterData();

            if (config('system.profiler')) {
                $html .= $this->renderDbData();
            }
        }

        return '<div>' . $html . '</div>';
    }

    /**
     * Render marks
     */
    protected function renderMarksData()
    {
        // vars
        $html   = '';
        $time    = 0;
        $memory    = 0;

        foreach ($this->marks as $mark) {
            $mark['time'] = $mark['time'] - $this->itime;
            $mark['memory']    = ($mark['memory'] - $this->imemory) / 1048576;

            $html .= '<li>'
                . sprintf(
                    'Time: %s (+%s), Memory: %0.3f MB (%s%0.3f) - %s',
                    $this->timeToString($mark['time']),
                    $this->timeToString($mark['time'] - $time),
                    $mark['memory'],
                    ($mark['memory'] > $memory ? '+' : ''),
                    ($mark['memory'] - $memory),
                    $mark['label']
                ) . '</li>';

            $time    = $mark['time'];
            $memory    = $mark['memory'];
        }

        return '<div>'
            .   '<h2>' . _t('Profiler') . '</h2>'
            .   '<div><ul>' . $html . '<ul></div>'
            . '</div>';
    }

    /**
     * Render router data
     */
    protected function renderRouterData()
    {
        $router = router();
        return '<div>'
            .   '<h2>' . _t('Router') . '</h2>'
            .   '<div><b>Route:</b> ' . $router->getRoute() . '</div>'
            .   '<div><b>Controller:</b> ' . $router->getControllerAsString() . '</div>'
            . '</div>';
    }

    /**
     * Render database data
     */
    protected function renderDbData()
    {
        $html = '';
        foreach (db()->getQueries() as $query) {
            $html .= '<li>'
                . preg_replace(
                    ['#(=|>|<)#', '#(?<!\w|>)([A-Z_]{2,})(?!\w)#x'],
                    ['<span class="red">$1</span>', '<span class="green">$1</span>'],
                    $query
                )
                . '</li>';
        }

        return '<div>'
            .   '<h2>' . _t('DB') . '</h2>'
            .   '<div><ul>' . $html . '<ul></div>'
            . '</div>';
    }

    /**
     * Time to string format
     * 
     * @param int $time
     * 
     * @return string
     */
    protected function timeToString(float $time): string
    {
        $micro = $time - floor($time);
        $time = floor($time);
        $format = '';

        // hours
        if ($time >= 3600) {
            $format .= intdiv($time, 3600) . ':';
            $time %= 3600;
        }
        // minutes
        $min = 0;
        if ($time >= 60) {
            $min  = intdiv($time, 60);
            $time %= 60;
        }
        if ($format) {
            $format .= sprintf("%02d:", $min);
        } elseif ($min) {
            $format .= $min . ':';
        }
        // seconds
        if ($format) {
            $format .= sprintf("%02d", $time);
        } elseif ($time) {
            $format .= $time;
        } else {
            $format .= '0';
        }
        // miliseconds
        if ($micro) {
            $format .= sprintf(".%03d", $micro * 1000);
        }

        return $format;
    }
}
