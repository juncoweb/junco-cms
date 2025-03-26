<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class UsysHelper
{
    /**
     * Get
     */
    public static function getUrl(string $url, ?string $redirect = null, array $args = [])
    {
        if (strpos($url, ',') !== false) {
            $url     = str_replace(' ', '', $url);
            $partial = explode(',', $url, 2);

            empty($partial[1])
                or parse_str($partial[1], $args);

            if ($redirect) {
                $args['redirect'] = urlencode($redirect);
            }

            return url($partial[0], $args, true);
        }

        return $url;
    }
}
