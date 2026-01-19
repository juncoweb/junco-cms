<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Tabs\TabsInterface;

class Tabs
{
    /**
     * Get
     */
    public static function get(string $snippet = '', string $id = '', array $options = []): TabsInterface
    {
        return snippet('tabs', $snippet, $id, $options);
    }
}
