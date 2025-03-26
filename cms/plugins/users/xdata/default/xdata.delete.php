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
    db()->safeExec("DELETE FROM `#__users_roles_labels_map` WHERE label_id IN (
		SELECT id
		FROM `#__users_roles_labels`
		WHERE extension_id = ?
	)", $xdata->extension_id);
    db()->safeExec("DELETE FROM `#__users_roles_labels` WHERE extension_id = ?", $xdata->extension_id);
};
