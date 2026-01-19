<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Extensions\XData\XData;

return function (XData $xdata) {
    $data = db()->query("
	SELECT
	 label_key ,
	 label_name ,
	 label_description
	FROM `#__users_roles_labels`
	WHERE extension_id = ?", $xdata->extension_id)->fetchAll();

    return $xdata->putData($data);
};
