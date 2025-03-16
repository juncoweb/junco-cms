<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
	$rows['shortcuts_allow_cache']['options'] = [
		-1 => _t('Inherit'),
		0 => _t('No'),
		1 => _t('Yes')
	];
};
