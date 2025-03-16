<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
	if (!empty($rows['locale'])) {
		$rows['locale'] = trim($rows['locale'], '/\\');
	}

	if (!empty($rows['use_gettext']) && !function_exists('gettext')) {
		$rows['use_gettext'] = false;
	}
};
