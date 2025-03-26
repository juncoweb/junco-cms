<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

/**
 * Has data
 *
 * @param int    $extension_id
 * @param string $extension_alias
 *
 * @return bool
 */
return function ($extension_id, $extension_alias) {
    // query
    return db()->safeFind("
	SELECT COUNT(*)
	FROM `#__menus`
	WHERE extension_id = $extension_id
	AND is_distributed = 1")->fetchColumn();
};
