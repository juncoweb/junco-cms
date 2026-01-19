<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Frontlist\Contract\BoxInterface;
use Junco\Frontlist\Contract\FrontlistInterface;

class Frontlist
{
    /**
     * Get
     */
    public static function get(string $snippet = ''): FrontlistInterface
    {
        return snippet('frontlist', $snippet);
    }

    /**
     * box
     */
    public static function getBox(string $snippet = '', string $id = ''): BoxInterface
    {
        if ($snippet === 'default') {
            $snippet = '';
        }

        return snippet('frontlist#box', $snippet ?: '', $id);
    }
}
