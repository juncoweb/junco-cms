<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Backlist\Contract\BacklistInterface;
use Junco\Backlist\Contract\BoxInterface;

class Backlist
{
    /**
     * Get
     */
    public static function get(string $snippet = ''): BacklistInterface
    {
        return snippet('backlist', $snippet);
    }

    /**
     * box
     */
    public static function getBox(string $snippet = '', string $id = ''): BoxInterface
    {
        return snippet('backlist#box', $snippet, $id);
    }
}
