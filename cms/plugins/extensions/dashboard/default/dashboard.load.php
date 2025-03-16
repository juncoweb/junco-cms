<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$dashboard) {
	$cache = cache();
	$html = $cache->get('extensions-updates#');

	if (!$html) {
		app('assets')->js('cms/plugins/extensions/dashboard/default/js/scripts.js');
		$html = '<div id="extensions-updates">'
			. '<form>'
			.  '<input type="hidden" name="option" value="1">'
			.  FormSecurity::getToken()
			. '</form>'
			. '</div>';
	}

	if ($html != 'null') {
		$dashboard->row($html);
	}
};
