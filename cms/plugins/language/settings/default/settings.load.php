<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
	$langs = new LanguageHelper();

	$rows['availables']['options'] = $langs->getAvailables(true);
	$rows['type']['options'] = ['Cookie', 'URL'];

	if (!function_exists('gettext')) {
		$rows['use_gettext']['help'] = '<span class="color-red">' . _t('The gettext library is not available.') . '</span>';
	}
};
