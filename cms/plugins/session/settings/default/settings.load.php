<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows = false) {
	$rows['handler']['options'] = [
		''		=> _t('Default'),
		'file'	=> _t('File'),
		'db'	=> _t('Database'),
	];
};
