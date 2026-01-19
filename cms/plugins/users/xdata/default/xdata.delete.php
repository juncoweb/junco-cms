<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Extensions\XData\XData;

return function (XData $xdata) {
    db()->exec("DELETE FROM `#__users_roles_labels_map` WHERE label_id IN (
		SELECT id
		FROM `#__users_roles_labels`
		WHERE extension_id = ?
	)", $xdata->extension_id);
    db()->exec("DELETE FROM `#__users_roles_labels` WHERE extension_id = ?", $xdata->extension_id);
};
