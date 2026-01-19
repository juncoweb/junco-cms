<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

include 'facades.php';
include 'constants.php';
include SYSTEM_STORAGE . 'etc/users-labels.php';

class System
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // initialize output buffering
        ob_get_level() or ob_start();

        // encoding
        mb_internal_encoding('UTF-8');
        ini_set('default_charset', 'UTF-8');
        //mb_http_output('pass');

        // timezone
        date_default_timezone_set(config('system.timezone'));
    }

    /**
     * Get
     */
    public function getLogPath(): string
    {
        return SYSTEM_STORAGE . config('system.log_path');
    }

    /**
     * Get
     */
    public function getTmpPath(): string
    {
        return SYSTEM_STORAGE . config('system.tmp_path');
    }

    /**
     * Get
     */
    public function getCgiPath(): string
    {
        return SYSTEM_ABSPATH . config('system.cgi_bin');
    }

    /**
     * Is demo
     */
    public function isDemo(): bool
    {
        return defined('IS_DEMO')
            ? (bool)constant('IS_DEMO')
            : false;
    }

    /**
     * Create and return an object instance for the view
     * 
     * @param bool $severe    If the output is a template, it will return the system's template.
     * 
     * @return object
     */
    public static function getOutput(bool $severe = false)
    {
        $format = router()->getFormat();

        switch ($format) {
            case 'blank':
                return Responder::asHttpBlank();

            case 'text':
                return Responder::asAjaxText();

            case 'json':
                return Responder::asAjaxJson();

            case 'template':
                return $severe
                    ? snippet('template')
                    : Template::get();

            default:
                return snippet($format);
        }
    }
}
