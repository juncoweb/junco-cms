<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Xjs
{
    /**
     * Reload Page
     */
    public static function reloadPage()
    {
        return [null, null, ['__reloadPage' => 1]];
    }

    /**
     * 
     */
    public static function redirectTo(string $url = '')
    {
        return [null, null, ['__redirectTo' => $url]];
    }

    /**
     * 
     */
    public static function goBack()
    {
        return [null, null, ['__goBack' => 1]];
    }

    /**
     * 
     */
    public static function response(string $message, int $code, $data = null)
    {
        return [$message, $code, $data];
    }
}
