<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Modal\ModalInterface;

class Modal
{
    /**
     * Get
     */
    public static function get(string $snippet = ''): ModalInterface
    {
        return snippet('modal', $snippet);
    }
}
