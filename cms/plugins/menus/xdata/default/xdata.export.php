<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$xdata) {
	$data = db()->safeFind("
	SELECT
	 menu_key ,
	 menu_default_path AS menu_path ,
	 menu_order ,
	 menu_url ,
	 menu_image ,
	 menu_hash ,
	 menu_params ,
	 status
	FROM `#__menus`
	WHERE extension_id = ?
	AND is_distributed = 1", $xdata->extension_id)->fetchAll();

	return $xdata->putData($data);
};
