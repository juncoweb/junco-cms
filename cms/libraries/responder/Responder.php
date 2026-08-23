<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Responder\Contract\AjaxJsonInterface;
use Junco\Responder\Contract\AjaxTextInterface;
use Junco\Responder\Contract\HttpBlankInterface;
use Junco\Responder\Contract\ResponderInterface;

class Responder
{
    /**
     * Create and return an object instance for the view
     * 
     * @param bool $severe    If the output is a template, it will return the system's template.
     * 
     * @return object
     */
    public static function get(bool $severe = false): ResponderInterface
    {
        $format = router()->getFormat();

        switch ($format) {
            case 'blank':
                return self::asHttpBlank();

            case 'text':
                return self::asAjaxText();

            case 'json':
                return self::asAjaxJson();

            case 'template':
                return $severe
                    ? snippet('template')
                    : Template::get();

            default:
                return snippet($format);
        }
    }

    /**
     * 
     */
    public static function asAjaxJson(): AjaxJsonInterface
    {
        return snippet('responder#ajax_json');
    }

    /**
     * 
     */
    public static function asAjaxText(): AjaxTextInterface
    {
        return snippet('responder#ajax_text');
    }

    /**
     * 
     */
    public static function asHttpBlank(): HttpBlankInterface
    {
        return snippet('responder#http_blank');
    }
}
