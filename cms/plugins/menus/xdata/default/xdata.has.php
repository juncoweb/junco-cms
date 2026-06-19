<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Menus\XStorage;

/**
 * Has data
 *
 * @param int    $extension_id
 * @param string $extension_alias
 *
 * @return bool
 */
return function ($extension_id, $extension_alias) {
    return (new XStorage)->has($extension_id);
};
