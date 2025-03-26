<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$xdata) {
    $data = db()->safeFind("
	SELECT
	 label_key ,
	 label_name ,
	 label_description
	FROM `#__users_roles_labels`
	WHERE extension_id = ?", $xdata->extension_id)->fetchAll();

    return $xdata->putData($data);
};
