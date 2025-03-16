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
	return (new Settings($extension_alias))->has();
};
