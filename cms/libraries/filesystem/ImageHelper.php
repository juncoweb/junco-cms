<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

class ImageHelper
{
    /**
     * Get
     *
     * @return array
     */
    public static function getResizeOptions(): array
    {
        return ['mode', 'size'];
    }
}
