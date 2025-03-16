<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
	// query
	$rows['btn_caption']['options'] = [
		'responsive' => _t('Responsive'),
		'visible' => _t('Visible'),
		'hidden' => _t('Hidden'),
	];
};
