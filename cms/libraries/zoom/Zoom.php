<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Zoom
{
    /**
     * Get
     */
    public static function get(string $snippet = '.table', string $id = ''): ZoomInterface
    {
        return snippet('zoom', $snippet, $id);
    }
}
