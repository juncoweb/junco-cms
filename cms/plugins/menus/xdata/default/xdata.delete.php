<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

/**
 * Delete
 *
 * @param object $xdata
 */
return function (&$xdata) {
	db()->safeExec("DELETE FROM `#__menus` WHERE extension_id = ?", $xdata->extension_id);
};
